<?php

namespace App\DataFixtures\Factory\Payment;

use App\Entity\Payment\TransferPayment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<TransferPayment>
 */
final class TransferPaymentFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'amount' => self::faker()->randomElement([240, 120, 80, 60]),
            'date' => self::faker()->dateTimeBetween('-3 months', '-1 week'),
            'label' => 'VIR '.self::faker()->word(),
            'comment' => self::faker()->text(),
        ];
    }

    public static function class(): string
    {
        return TransferPayment::class;
    }
}
