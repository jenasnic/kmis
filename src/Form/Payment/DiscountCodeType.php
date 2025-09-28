<?php

namespace App\Form\Payment;

use App\Entity\Payment\DiscountCode;
use App\Enum\RefundHelpEnum;
use App\Form\Type\EnumType;
use App\Service\Configuration\RefundHelpManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<DiscountCode>
 */
class DiscountCodeType extends AbstractType
{
    public function __construct(
        private readonly RefundHelpManager $refundHelpManager,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class)
            ->add('refundHelps', EnumType::class, [
                'enum' => RefundHelpEnum::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function (RefundHelpEnum $choice) {
                    return $this->refundHelpManager->getLabel($choice);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DiscountCode::class,
            'label_format' => 'form.discountCode.%name%',
        ]);
    }
}
