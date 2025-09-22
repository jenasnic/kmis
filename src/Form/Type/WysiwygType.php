<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<string>
 */
class WysiwygType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['size'] = $options['size'] ?? 'large';
        $view->vars['toolbar'] = $options['toolbar'] ?? '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('size');
        $resolver->setAllowedValues('size', ['large', 'medium', 'small']);

        $resolver->setDefined('toolbar');
        $resolver->setAllowedTypes('toolbar', 'string');

        $resolver->setDefault('required', false);
    }

    public function getParent()
    {
        return TextareaType::class;
    }
}
