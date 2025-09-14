<?php

namespace App\Form\Content;

use App\Domain\Model\Content\ConfigurationText;
use App\Form\ConfigurationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<ConfigurationText>
 */
class ConfigurationTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('homePresentation', ConfigurationType::class, ['required' => false])
            ->add('contact', ConfigurationType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ConfigurationText::class,
            'label_format' => 'form.configurationText.%name%',
        ]);
    }
}
