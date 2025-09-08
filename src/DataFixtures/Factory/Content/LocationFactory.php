<?php

namespace App\DataFixtures\Factory\Content;

use App\DataFixtures\Factory\AddressFactory;
use App\Entity\Content\Location;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Location>
 */
final class LocationFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'active' => self::faker()->boolean(),
            'name' => self::faker()->city(),
            'rank' => self::faker()->randomNumber(),
            'address' => AddressFactory::createOne(),
            'latitude' => self::faker()->latitude(),
            'longitude' => self::faker()->longitude(),
        ];
    }

    public static function class(): string
    {
        return Location::class;
    }
}
