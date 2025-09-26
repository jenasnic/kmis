<?php

namespace App\DataFixtures\Factory\Payment;

use App\Entity\Payment\RefundHelpPayment;
use App\Enum\RefundHelpEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<RefundHelpPayment>
 */
final class RefundHelpPaymentFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        /** @var RefundHelpEnum $refundHelp */
        $refundHelp = self::faker()->randomElement(RefundHelpEnum::cases());

        return [
            'amount' => self::faker()->randomElement([240, 120, 80, 60]),
            'date' => self::faker()->dateTimeBetween('-3 months', '-1 week'),
            'reference' => 'REF-'.self::faker()->numberBetween(1000, 9999),
            'refundHelp' => $refundHelp,
            'comment' => self::faker()->text(),
        ];
    }

    public static function class(): string
    {
        return RefundHelpPayment::class;
    }
}
