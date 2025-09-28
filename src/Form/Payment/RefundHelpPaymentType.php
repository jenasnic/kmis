<?php

namespace App\Form\Payment;

use App\Entity\Adherent;
use App\Entity\Payment\RefundHelpPayment;
use App\Entity\Season;
use App\Enum\RefundHelpEnum;
use App\Form\DataMapper\Payment\RefundHelpPaymentDataMapper;
use App\Form\Type\EnumType;
use App\Service\Configuration\RefundHelpManager;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractPaymentType<RefundHelpPayment>
 */
class RefundHelpPaymentType extends AbstractPaymentType
{
    public function __construct(
        private readonly RefundHelpManager $refundHelpManager,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('amount')
            ->add('refundHelp', EnumType::class, [
                'enum' => RefundHelpEnum::class,
                'expanded' => true,
                'choice_label' => function (RefundHelpEnum $choice) {
                    return $this->refundHelpManager->getLabel($choice);
                },
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
        return new RefundHelpPaymentDataMapper($this->refundHelpManager, $adherent, $season);
    }
}
