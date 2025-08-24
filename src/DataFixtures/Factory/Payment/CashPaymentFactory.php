<?php

namespace App\DataFixtures\Factory\Payment;

use App\Entity\Payment\CashPayment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<CashPayment>
 */
final class CashPaymentFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'amount' => self::faker()->randomElement([240, 120, 80, 60]),
            'date' => self::faker()->dateTimeBetween('-3 months', '-1 week'),
            'comment' => self::faker()->text(),
        ];
    }

    public static function class(): string
    {
        return CashPayment::class;
    }
}
