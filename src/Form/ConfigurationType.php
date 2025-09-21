<?php

namespace App\Form;

use App\Entity\Configuration;
use App\Form\Type\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<Configuration>
 */
class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', WysiwygType::class, [
                'label' => false,
                'required' => false,
                'small_size' => $options['small_size'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('small_size');
        $resolver->setAllowedTypes('small_size', 'bool');

        $resolver->setDefaults([
            'data_class' => Configuration::class,
            'small_size' => false,
        ]);
    }
}
