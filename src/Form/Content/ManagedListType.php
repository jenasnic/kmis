<?php

namespace App\Form\Content;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template T of object
 *
 * @template-extends AbstractType<array<T>>
 */
class ManagedListType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['withActive', 'entityClass'])
            ->setAllowedTypes('withActive', 'boolean')
            ->setDefaults([
                'label' => false,
                'entry_type' => ListItemType::class,
                'entry_options' => function (Options $options): array {
                    return [
                        'label' => false,
                        'withActive' => $options['withActive'],
                        'data_class' => $options['entityClass'],
                    ];
                },
                'allow_add' => false,
                'allow_delete' => false,
            ])
        ;
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
