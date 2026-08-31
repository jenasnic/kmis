<?php

namespace App\Form\Content;

use App\Domain\Model\Content\DiscountCodeConfiguration;
use App\Entity\Payment\DiscountCode;
use App\Form\Payment\DiscountCodeType;
use App\Form\Type\BulmaCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<array<DiscountCode>>
 */
class DiscountCodeConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('discountCodes', BulmaCollectionType::class, [
                'label' => false,
                'entry_type' => DiscountCodeType::class,
                'entry_options' => ['label' => false],
                'block_prefix' => 'discount_code_list',
                'allow_add' => true,
                'allow_delete' => true,
                'add_label_id' => 'form.discountCodeConfiguration.addCode',
                'collection_css_class' => 'discount-code-list',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DiscountCodeConfiguration::class,
            'label_format' => 'form.discountCodeConfiguration.%name%',
        ]);
    }
}
