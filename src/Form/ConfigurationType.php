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
                'size' => $options['size'] ?? 'large',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('size');
        $resolver->setAllowedValues('size', ['large', 'medium', 'small']);

        $resolver->setDefaults([
            'data_class' => Configuration::class,
        ]);
    }
}
