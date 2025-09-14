<?php

namespace App\Domain\Command\Back\Content;

use App\Entity\Content\News;
use App\Repository\Content\NewsRepository;
use App\Service\File\FileManager;

final class SaveNewsHandler
{
    public function __construct(
        private readonly NewsRepository $newsRepository,
        private readonly FileManager $fileManager,
    ) {
    }

    public function handle(SaveNewsCommand $command): void
    {
        $this->processFile($command->news);

        $this->newsRepository->add($command->news, true);
    }

    private function processFile(News $news): void
    {
        $pictureFile = $news->getPictureFile();
        if (null !== $pictureFile) {
            $this->fileManager->upload($news, $pictureFile, 'pictureUrl');
        }
    }
}
