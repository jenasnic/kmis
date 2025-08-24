<?php

namespace App\DataFixtures\Factory\Payment;

use App\Entity\Payment\PassPayment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PassPayment>
 */
final class PassPaymentFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'amount' => self::faker()->randomElement([240, 120, 80, 60]),
            'date' => self::faker()->dateTimeBetween('-3 months', '-1 week'),
            'number' => 'PASS-'.self::faker()->numberBetween(1000, 9999),
            'comment' => self::faker()->text(),
        ];
    }

    public static function class(): string
    {
        return PassPayment::class;
    }
}
