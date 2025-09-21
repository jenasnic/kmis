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
            ->add('enablePassCitizen', CheckboxType::class, ['required' => false])
            ->add('passCitizenLabel', TextType::class, ['required' => false])
            ->add('passCitizenHelpText', WysiwygType::class, [
                'required' => false,
                'small_size' => true,
            ])
            ->add('enablePassSport', CheckboxType::class, ['required' => false])
            ->add('passSportLabel', TextType::class, ['required' => false])
            ->add('passSportHelpText', WysiwygType::class, [
                'required' => false,
                'small_size' => true,
            ])
            ->add('enableCCAS', CheckboxType::class, ['required' => false])
            ->add('CCASLabel', TextType::class, ['required' => false])
            ->add('CCASHelpText', WysiwygType::class, [
                'required' => false,
                'small_size' => true,
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
