<?php

namespace App\Form\Content;

use App\Domain\Model\Content\RefundHelpConfiguration;
use App\Form\Type\MaskedType;
use App\Form\Type\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<RefundHelpConfiguration>
 */
class RefundHelpConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('passCitizenEnable', CheckboxType::class, ['required' => false])
            ->add('passCitizenAmount', MaskedType::class, [
                'required' => false,
                'is_numeric' => true,
                'mask' => '000',
            ])
            ->add('passCitizenHelpText', WysiwygType::class, [
                'required' => false,
                'size' => 'small',
                'toolbar' => 'code | bold underline link forecolor',
            ])
            ->add('passSportEnable', CheckboxType::class, ['required' => false])
            ->add('passSportAmount', MaskedType::class, [
                'required' => false,
                'is_numeric' => true,
                'mask' => '000',
            ])
            ->add('passSportHelpText', WysiwygType::class, [
                'required' => false,
                'size' => 'small',
                'toolbar' => 'code | bold underline link forecolor',
            ])
            ->add('ccasEnable', CheckboxType::class, ['required' => false])
            ->add('ccasAmount', MaskedType::class, [
                'required' => false,
                'is_numeric' => true,
                'mask' => '000',
            ])
            ->add('ccasHelpText', WysiwygType::class, [
                'required' => false,
                'size' => 'small',
                'toolbar' => 'code | bold underline link forecolor',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RefundHelpConfiguration::class,
            'label_format' => 'form.refundHelpConfiguration.%name%',
        ]);
    }
}
