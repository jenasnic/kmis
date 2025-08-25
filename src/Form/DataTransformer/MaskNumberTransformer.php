<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements DataTransformerInterface<string|float|int, string>
 */
class MaskNumberTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly int $floatPrecision,
    ) {
    }

    public function transform(mixed $value): mixed
    {
        return (string) $value;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (null === $value) {
            return 0;
        }

        $number = preg_replace('/[^0-9,\.]/', '', $value);
        if (null === $number) {
            return 0;
        }

        $number = str_replace(',', '.', $number);
        if (is_numeric($number)) {
            return round((float) $number, $this->floatPrecision);
        }

        return 0;
    }
}
