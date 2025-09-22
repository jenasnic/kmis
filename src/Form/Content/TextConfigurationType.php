<?php

namespace App\Form\Content;

use App\Domain\Model\Content\TextConfiguration;
use App\Form\ConfigurationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<TextConfiguration>
 */
class TextConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('homePresentation', ConfigurationType::class, ['required' => false])
            ->add('contact', ConfigurationType::class, [
                'required' => false,
                'size' => 'medium',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TextConfiguration::class,
            'label_format' => 'form.textConfiguration.%name%',
        ]);
    }
}
