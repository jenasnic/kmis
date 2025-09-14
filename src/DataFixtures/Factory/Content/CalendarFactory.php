<?php

namespace App\DataFixtures\Factory\Content;

use App\Entity\Content\Calendar;
use App\Enum\DayOfWeekEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Calendar>
 */
final class CalendarFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'day' => self::faker()->randomElement(DayOfWeekEnum::cases()),
            'location' => LocationFactory::new(),
        ];
    }

    public static function class(): string
    {
        return Calendar::class;
    }
}
