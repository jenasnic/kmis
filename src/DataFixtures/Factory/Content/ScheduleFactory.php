<?php

namespace App\DataFixtures\Factory\Content;

use App\Entity\Content\Schedule;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Schedule>
 */
final class ScheduleFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'calendar' => CalendarFactory::new(),
            'end' => self::faker()->text(15),
            'start' => self::faker()->text(15),
        ];
    }

    public static function class(): string
    {
        return Schedule::class;
    }
}
