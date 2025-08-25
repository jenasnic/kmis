<?php

namespace App\DataFixtures\Factory;

use App\Entity\User;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public const DEFAULT_PASSWORD = 'pwd';

    private Generator $faker;

    private AsciiSlugger $slugger;

    private int $counter = 0;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();

        $this->faker = Factory::create('fr_FR');
        $this->slugger = new AsciiSlugger('fr_FR');
    }

    protected function defaults(): array|callable
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();

        $email = sprintf(
            '%s.%s.%d@yopmail.com',
            $this->slugger->slug($firstName),
            $this->slugger->slug($lastName),
            ++$this->counter
        );

        $enabled = self::faker()->boolean(80);

        return [
            'email' => strtolower($email),
            'password' => self::DEFAULT_PASSWORD,
            'enabled' => $enabled,
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function (User $user, array $attributes) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $attributes['password']));
        });
    }

    public static function class(): string
    {
        return User::class;
    }
}
