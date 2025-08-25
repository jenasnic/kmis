<?php

namespace App\DataFixtures\Factory\Content;

use App\Entity\Content\News;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Filesystem\Filesystem;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<News>
 */
final class NewsFactory extends PersistentProxyObjectFactory
{
    private string $newsPicture;

    private Generator $faker;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly string $uploadPath,
    ) {
        parent::__construct();

        $this->newsPicture = __DIR__.'/../data/news.jpg';

        $this->faker = Factory::create('fr_FR');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        $filePath = $this->uploadPath.News::PICTURE_FOLDER.str_replace('.', '', uniqid('', true)).'.jpg';
        $this->filesystem->copy($this->newsPicture, $filePath);

        return [
            'title' => $this->faker->words(4, true),
            'content' => $this->faker->text(),
            'details' => $this->faker->words(3, true),
            'active' => $this->faker->boolean(80),
            'pictureUrl' => $filePath,
        ];
    }

    public static function class(): string
    {
        return News::class;
    }
}
