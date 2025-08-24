<?php

namespace App\DataFixtures\Factory\Payment;

use App\Entity\Payment\PriceOption;
use Faker\Factory;
use Faker\Generator;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PriceOption>
 */
final class PriceOptionFactory extends PersistentProxyObjectFactory
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
        return PriceOption::class;
    }
}
