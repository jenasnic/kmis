<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @phpstan-type _ChartDataType array{
 *     identifier: string,
 *     label: string,
 *     value: int|float,
 *     percent: int,
 *     color: string,
 *     x: float,
 *     y: float,
 *     large: boolean,
 * }
 */
class ChartExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getChartData', [$this, 'getChartData']),
        ];
    }

    /**
     * @param array<array<string, mixed>> $data
     * @param array<string> $colors
     *
     * @return array<_ChartDataType>
     */
    public function getChartData(array $data, string $identifierKey, string $labelKey, string $valueKey, array $colors): array
    {
        // @phpstan-ignore-next-line
        $total = array_reduce($data, fn (float $sum, array $item) => $sum + $item[$valueKey], 0);

        $angle = $colorPicker = 0;

        $result = [];
        foreach ($data as $item) {
            // @phpstan-ignore-next-line
            $valueAngle = (float) 360 * $item[$valueKey] / $total;
            // @phpstan-ignore-next-line
            $percent = 100 * $item[$valueKey] / $total;
            $angle += $valueAngle;
            $result[] = [
                'identifier' => $item[$identifierKey],
                'label' => $item[$labelKey],
                'value' => $item[$valueKey],
                'percent' => (int) $percent,
                'color' => $colors[$colorPicker++ % count($colors)],
                'x' => cos(deg2rad($angle)),
                'y' => sin(deg2rad($angle)),
                'large' => $valueAngle > 180,
            ];
        }

        // @phpstan-ignore-next-line
        return $result;
    }
}
