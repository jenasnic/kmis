<?php

namespace App\DataFixtures\Factory;

use App\Entity\Season;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Season>
 */
final class SeasonFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        $date = self::faker()->dateTimeBetween('-4 years', '+1 year');
        $year = $date->format('Y');
        $nextYear = $date->add(\DateInterval::createFromDateString('+1 year'))->format('Y');

        return [
            'label' => $year,
            'startDate' => new \DateTime($year.'-09-01'),
            'endDate' => new \DateTime($nextYear.'-08-31'),
        ];
    }

    public static function class(): string
    {
        return Season::class;
    }
}
