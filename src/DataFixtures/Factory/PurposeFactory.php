<?php

namespace App\DataFixtures\Factory;

use App\Entity\Purpose;
use Faker\Factory;
use Faker\Generator;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Purpose>
 */
final class PurposeFactory extends PersistentProxyObjectFactory
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
            'label' => $this->faker->words(4, true),
        ];
    }

    public static function class(): string
    {
        return Purpose::class;
    }
}
