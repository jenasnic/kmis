<?php

namespace App\Form\Content;

use App\Domain\Model\Content\DiscountConfiguration;
use App\Form\Type\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<DiscountConfiguration>
 */
class DiscountConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('passCitizenEnable', CheckboxType::class, ['required' => false])
            ->add('passCitizenLabel', TextType::class, ['required' => false])
            ->add('passCitizenHelpText', WysiwygType::class, [
                'required' => false,
                'size' => 'small',
                'toolbar' => 'code | bold underline link forecolor',
            ])
            ->add('passCitizenFileLabel', TextType::class, ['required' => false])
            ->add('passSportEnable', CheckboxType::class, ['required' => false])
            ->add('passSportLabel', TextType::class, ['required' => false])
            ->add('passSportHelpText', WysiwygType::class, [
                'required' => false,
                'size' => 'small',
                'toolbar' => 'code | bold underline link forecolor',
            ])
            ->add('passSportFileLabel', TextType::class, ['required' => false])
            ->add('ccasEnable', CheckboxType::class, ['required' => false])
            ->add('ccasLabel', TextType::class, ['required' => false])
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
            'data_class' => DiscountConfiguration::class,
            'label_format' => 'form.discountConfiguration.%name%',
        ]);
    }
}
