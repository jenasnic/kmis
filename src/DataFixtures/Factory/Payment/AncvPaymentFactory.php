<?php

namespace App\DataFixtures\Factory\Payment;

use App\Entity\Payment\AncvPayment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<AncvPayment>
 */
final class AncvPaymentFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'amount' => self::faker()->randomElement([240, 120, 80, 60]),
            'date' => self::faker()->dateTimeBetween('-3 months', '-1 week'),
            'number' => 'ANCV-'.self::faker()->numberBetween(100000, 999999),
            'comment' => self::faker()->text(),
        ];
    }

    public static function class(): string
    {
        return AncvPayment::class;
    }
}
