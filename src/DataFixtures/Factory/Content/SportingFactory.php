<?php

namespace App\DataFixtures\Factory\Content;

use App\Entity\Content\Sporting;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Sporting>
 */
final class SportingFactory extends PersistentProxyObjectFactory
{
    private string $sportingPicture;

    private Generator $faker;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly string $uploadPath,
    ) {
        parent::__construct();

        $this->sportingPicture = __DIR__.'/../data/sporting.jpg';

        $this->faker = Factory::create('fr_FR');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        $fileName = str_replace('.', '', uniqid('', true)).'.jpg';
        $filePath = $this->uploadPath.Sporting::PICTURE_FOLDER.DIRECTORY_SEPARATOR.$fileName;
        $this->filesystem->copy($this->sportingPicture, $filePath);

        return [
            'active' => self::faker()->boolean(80),
            'name' => $this->faker->words(2, true),
            'tagline' => $this->faker->words(6, true),
            'content' => $this->faker->text(),
            'pictureUrl' => $fileName,
        ];
    }

    public static function class(): string
    {
        return Sporting::class;
    }
}
