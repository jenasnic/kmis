<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template T of object
 *
 * @template-extends AbstractType<array<T>>
 */
class BulmaCollectionType extends AbstractType
{
    /**
     * @param array<string, mixed> $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $collectionCssClasses = ['collection-type'];

        if (!empty($options['allow_add'])) {
            $collectionCssClasses[] = 'collection-type--with-add';
        }

        if (!empty($options['sortable'])) {
            $collectionCssClasses[] = 'collection-type--sortable';
        }

        if (!empty($options['collection_css_class'])) {
            $collectionCssClasses[] = $options['collection_css_class'];
        }

        $view->vars['add_label_id'] = $options['add_label_id'];
        $view->vars['collection_css_classes'] = implode(' ', $collectionCssClasses);
        $view->vars['remove_button_position'] = $options['remove_button_position'];
        $view->vars['sortable'] = $options['sortable'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined(['add_label_id', 'collection_css_class', 'remove_button_position', 'sortable'])
            ->setAllowedTypes('add_label_id', 'string')
            ->setAllowedTypes('collection_css_class', 'string')
            ->setAllowedValues('remove_button_position', ['start', 'end'])
            ->setAllowedTypes('sortable', 'boolean')
            ->setDefaults([
                'add_label_id' => 'global.add',
                'collection_css_class' => '',
                'remove_button_position' => 'end',
                'sortable' => false,
                'block_prefix' => 'bulma_collection',
            ])
        ;
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
