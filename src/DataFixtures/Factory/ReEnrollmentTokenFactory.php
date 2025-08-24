<?php

namespace App\DataFixtures\Factory;

use App\Entity\ReEnrollmentToken;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<ReEnrollmentToken>
 */
final class ReEnrollmentTokenFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'id' => substr(uniqid().bin2hex(random_bytes(20)), 0, 55),
            'expiresAt' => new \DateTime('+3 months'),
        ];
    }

    public static function class(): string
    {
        return ReEnrollmentToken::class;
    }
}
