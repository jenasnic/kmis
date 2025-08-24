<?php

namespace App\DataFixtures\Factory;

use App\Entity\Emergency;
use Faker\Factory;
use Faker\Generator;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Emergency>
 */
final class EmergencyFactory extends PersistentProxyObjectFactory
{
    private Generator $faker;

    public function __construct()
    {
        parent::__construct();

        $this->faker = Factory::create('fr_FR');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'firstName' => $this->faker->firstName(),
            'lastName' => $this->faker->lastName(),
            'phone' => $this->faker->numerify('06 ## ## ## ##'),
        ];
    }

    public static function class(): string
    {
        return Emergency::class;
    }
}
