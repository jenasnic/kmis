<?php

namespace App\Form;

use App\Entity\Payment\PriceOption;
use App\Entity\Purpose;
use App\Entity\Registration;
use App\Enum\RefundHelpEnum;
use App\Enum\RegistrationTypeEnum;
use App\Form\Type\BulmaFileType;
use App\Form\Type\EnumType;
use App\Repository\PurposeRepository;
use App\Service\Configuration\RefundHelpManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @template-extends AbstractType<Registration>
 */
abstract class AbstractRegistrationType extends AbstractType
{
    public function __construct(
        protected RefundHelpManager $refundHelpManager,
        protected RouterInterface $router,
    ) {
    }

    abstract protected function showPassSportHelp(): bool;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $refundHelpConfiguration = $this->refundHelpManager->getRefundHelpConfiguration();

        $builder
            ->add('comment', TextareaType::class, [
                'required' => false,
            ])
            ->add('copyrightAuthorization', ChoiceType::class, [
                'choices' => [
                    'global.yes' => true,
                    'global.no' => false,
                ],
                'expanded' => true,
                'required' => true,
            ])
            ->add('purpose', EntityType::class, [
                'class' => Purpose::class,
                'choice_label' => 'label',
                'query_builder' => function (PurposeRepository $purposeRepository) {
                    return $purposeRepository->createQueryBuilder('purpose')->orderBy('purpose.rank');
                },
            ])
            ->add('emergency', EmergencyType::class)
            ->add('withLegalRepresentative', CheckboxType::class, [
                'required' => false,
                'false_values' => [null, '0', 'false'],
            ])
            ->add('registrationType', EnumType::class, [
                'enum' => RegistrationTypeEnum::class,
                'label' => false,
                'expanded' => true,
            ])
        ;

        if ($refundHelpConfiguration->ccasEnable) {
            $builder->add('useCCAS', CheckboxType::class, [
                'label' => $this->refundHelpManager->getLabel(RefundHelpEnum::CCAS),
                'required' => false,
                'help' => $refundHelpConfiguration->ccasHelpText,
                'help_html' => true,
            ]);
        }

        $builder->get('withLegalRepresentative')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                if (null === $form->getParent()) {
                    throw new \LogicException('invalid parent');
                }

                $this->toggleLegalRepresentative($form->getParent(), true === $form->getData());
            }
        );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($refundHelpConfiguration) {
            /** @var Registration $registration */
            $registration = $event->getData();
            $form = $event->getForm();

            $priceOptionOptions = [
                'class' => PriceOption::class,
                'choice_label' => function (PriceOption $priceOption) {
                    return sprintf('%s - %d€', $priceOption->getLabel(), $priceOption->getAmount());
                },
                'choices' => $registration->getSeason()->getPriceOptions()->toArray(),
            ];

            $form->add('priceOption', EntityType::class, $priceOptionOptions);

            $this->toggleLegalRepresentative($form, $registration->isWithLegalRepresentative());

            if ($refundHelpConfiguration->passCitizenEnable) {
                $downloadPassCitizenUri = (null !== $registration->getId() && null !== $registration->getPassCitizenUrl())
                    ? $this->router->generate('bo_download_pass_citizen', ['registration' => $registration->getId()])
                    : null
                ;

                $this->processPassField(
                    $form,
                    $registration->isUsePassCitizen(),
                    'usePassCitizen',
                    $this->refundHelpManager->getLabel(RefundHelpEnum::PASS_CITIZEN),
                    $refundHelpConfiguration->passCitizenHelpText,
                    'passCitizenFile',
                    $downloadPassCitizenUri,
                );
            }

            if ($refundHelpConfiguration->passSportEnable) {
                $downloadPassSportUri = (null !== $registration->getId() && null !== $registration->getPassSportUrl())
                    ? $this->router->generate('bo_download_pass_sport', ['registration' => $registration->getId()])
                    : null
                ;

                $this->processPassField(
                    $form,
                    $registration->isUsePassSport(),
                    'usePassSport',
                    $this->refundHelpManager->getLabel(RefundHelpEnum::PASS_SPORT),
                    $refundHelpConfiguration->passSportHelpText,
                    'passSportFile',
                    $downloadPassSportUri,
                );
            }
        });
    }

    /**
     * @param FormBuilderInterface<Registration|null> $builder
     * @param array<string, mixed> $options
     */
    protected function addInternalFields(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('privateNote', TextareaType::class, [
                'required' => false,
            ])
            ->add('licenceNumber', TextType::class, [
                'required' => false,
            ])
            ->add('licenceDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('registeredAt', DateType::class, [
                'widget' => 'single_text',
            ])
        ;
    }

    /**
     * @param FormInterface<Registration> $form
     */
    protected function processPassField(
        FormInterface $form,
        bool $usePass,
        string $checkboxFieldName,
        ?string $label,
        ?string $helpText,
        string $uploadFieldName,
        ?string $downloadUri = null,
    ): void {
        $passOptions = [
            'label' => $label ?? $checkboxFieldName,
            'required' => false,
            'false_values' => [null, '0', 'false'],
            'auto_initialize' => false,
        ];

        if ($this->showPassSportHelp()) {
            $passOptions['help'] = $helpText;
            $passOptions['help_html'] = true;
        }

        $subBuilder = $form->getConfig()->getFormFactory()->createNamedBuilder($checkboxFieldName, CheckboxType::class, null, $passOptions);

        $subBuilder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($downloadUri, $uploadFieldName) {
                $form = $event->getForm();

                if (null === $form->getParent()) {
                    throw new \LogicException('invalid parent');
                }

                $this->togglePassFile($form->getParent(), $uploadFieldName, true === $form->getData(), $downloadUri);
            }
        );

        $form->add($subBuilder->getForm());
        $this->togglePassFile($form, $uploadFieldName, $usePass, $downloadUri);
    }

    /**
     * @param FormInterface<Registration> $form
     */
    protected function toggleLegalRepresentative(FormInterface $form, bool $state): void
    {
        if (!$state) {
            $form->remove('legalRepresentative');

            return;
        }

        $form->add('legalRepresentative', LegalRepresentativeType::class);
    }

    /**
     * @param FormInterface<Registration> $form
     */
    protected function togglePassFile(FormInterface $form, string $fieldName, bool $state, ?string $downloadUri = null): void
    {
        if (!$state) {
            $form->remove($fieldName);

            return;
        }

        $options = [
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'image/gif',
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                        'application/pdf',
                    ],
                ]),
            ],
        ];

        if (null !== $downloadUri) {
            $options['download_uri'] = $downloadUri;
            $options['required'] = false;
        } else {
            $options['constraints'][] = new NotNull();
        }

        $form->add($fieldName, BulmaFileType::class, $options);
    }
}
