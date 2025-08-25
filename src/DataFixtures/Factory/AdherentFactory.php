<?php

namespace App\DataFixtures\Factory;

use App\Entity\Adherent;
use App\Enum\GenderEnum;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Adherent>
 */
final class AdherentFactory extends PersistentProxyObjectFactory
{
    private string $malePicture;
    private string $femalePicture;

    private int $counter;

    private Generator $faker;

    private AsciiSlugger $slugger;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly string $uploadPath,
    ) {
        parent::__construct();

        $this->malePicture = __DIR__.'/data/male.jpg';
        $this->femalePicture = __DIR__.'/data/female.jpg';
        $this->counter = 0;

        $this->faker = Factory::create('fr_FR');
        $this->slugger = new AsciiSlugger('fr_FR');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        /** @var string $gender */
        $gender = $this->faker->randomElement(GenderEnum::getAll());
        $firstName = $this->faker->firstName(strtolower($gender));
        $lastName = $this->faker->lastName();

        $email = sprintf(
            '%s.%s.%d@yopmail.com',
            $this->slugger->slug($firstName),
            $this->slugger->slug($lastName),
            ++$this->counter
        );

        $pictureFixture = (GenderEnum::MALE === $gender) ? $this->malePicture : $this->femalePicture;
        $filePath = $this->uploadPath.str_replace('.', '', uniqid('', true)).'.jpg';
        $this->filesystem->copy($pictureFixture, $filePath);

        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'gender' => $gender,
            'birthDate' => $this->faker->dateTimeBetween('-55 years', '-18 years'),
            'phone' => $this->faker->numerify('06 ## ## ## ##'),
            'email' => strtolower($email),
            'address' => AddressFactory::createOne(),
            'pseudonym' => $firstName.substr($lastName, 0, 1),
            'pictureUrl' => $filePath,
        ];
    }

    public static function class(): string
    {
        return Adherent::class;
    }
}
