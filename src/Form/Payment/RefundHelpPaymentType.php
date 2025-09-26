<?php

namespace App\Form\Payment;

use App\Entity\Adherent;
use App\Entity\Payment\RefundHelpPayment;
use App\Entity\Season;
use App\Enum\RefundHelpEnum;
use App\Form\DataMapper\Payment\RefundHelpPaymentDataMapper;
use App\Service\Configuration\RefundHelpManager;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @template-extends AbstractPaymentType<RefundHelpPayment>
 */
class RefundHelpPaymentType extends AbstractPaymentType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RefundHelpManager $refundHelpManager,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('amount')
            ->add('refundHelp', ChoiceType::class, [
                'expanded' => true,
                'choices' => $this->buildChoices(),
            ])
            ->add('reference', TextType::class, [
                'required' => false,
                'help' => 'form.payment.refundHelp.referenceHelp',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => RefundHelpPayment::class,
            'label_format' => 'form.payment.refundHelp.%name%',
        ]);
    }

    protected function getDataMapper(Adherent $adherent, Season $season): DataMapperInterface
    {
        $refundHelpConfiguration = $this->refundHelpManager->getRefundHelpConfiguration();

        return new RefundHelpPaymentDataMapper($refundHelpConfiguration, $adherent, $season);
    }

    /**
     * @return array<string, string>
     */
    private function buildChoices(): array
    {
        $refundHelpConfiguration = $this->refundHelpManager->getRefundHelpConfiguration();

        $result = [];
        foreach (RefundHelpEnum::cases() as $refundHelp) {
            $amount = $refundHelpConfiguration->getAmount($refundHelp);
            if (empty($amount)) {
                continue;
            }

            $label = sprintf('%s (%d €)', $refundHelp->trans($this->translator), $amount);

            $result[$label] = $refundHelp->value;
        }

        return $result;
    }
}
