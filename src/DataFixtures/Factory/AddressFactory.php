<?php

namespace App\DataFixtures\Factory;

use App\ValueObject\Address;
use Faker\Factory;
use Faker\Provider\fr_FR\Address as FakerAddress;

final class AddressFactory
{
    /**
     * @param array<string, mixed> $attributes
     */
    final public static function createOne(array $attributes = []): Address
    {
        $faker = Factory::create('fr_FR');

        $department = FakerAddress::department();
        $departmentNumber = (string) array_key_first($department);

        /** @var string $streetPrefix */
        $streetPrefix = FakerAddress::streetPrefix();
        $defaultAttributes = [
            'street' => sprintf('%u %s %s %s', $faker->numberBetween(1, 50), $streetPrefix, $faker->firstName(), $faker->lastName()),
            'zipCode' => str_pad($departmentNumber, 5, '0'),
            'city' => $faker->city(),
        ];

        /** @var array{street: string, zipCode: string, city: string} $attributes */
        $attributes = array_merge($defaultAttributes, $attributes);

        return new Address(
            $attributes['street'],
            null,
            $attributes['zipCode'],
            $attributes['city'],
        );
    }
}
