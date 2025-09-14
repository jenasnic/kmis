<?php

namespace App\Form\Type;

use App\Enum\TranslatableEnumInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template T of \BackedEnum
 *
 * @template-extends AbstractType<T>
 */
class EnumType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('enum')
            ->setDefined(['label_prefix', 'use_name_as_label'])
            ->setAllowedValues('label_prefix', ['null', 'string'])
            ->setAllowedTypes('enum', 'string')
            ->setAllowedValues('enum', function ($value) {
                /** @var class-string<\BackedEnum> $value */
                return enum_exists($value);
            })
            ->setDefaults([
                'use_name_as_label' => false,
                'choices' => function (Options $options): array {
                    /** @var \BackedEnum $class */
                    $class = $options['enum'];

                    return $class::cases();
                },
                'choice_label' => function (Options $options): \Closure {
                    return static function (\BackedEnum $value) use ($options): string {
                        if (isset($options['label_prefix']) && is_string($options['label_prefix'])) {
                            return sprintf('%s.%s', $options['label_prefix'], strtolower($value->name));
                        }

                        if ($value instanceof TranslatableEnumInterface) {
                            return $value->getTranslationKey();
                        }

                        return $options['use_name_as_label'] ? $value->name : (string) $value->value;
                    };
                },
            ])
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
