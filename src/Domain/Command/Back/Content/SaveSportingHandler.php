<?php

namespace App\Domain\Command\Back\Content;

use App\Entity\Content\Sporting;
use App\Repository\Content\SportingRepository;
use App\Service\File\FileManager;

final class SaveSportingHandler
{
    public function __construct(
        private readonly SportingRepository $sportingRepository,
        private readonly FileManager $fileManager,
    ) {
    }

    public function handle(SaveSportingCommand $command): void
    {
        $this->processFile($command->sporting);

        $this->sportingRepository->add($command->sporting, true);
    }

    private function processFile(Sporting $sporting): void
    {
        $pictureFile = $sporting->getPictureFile();
        if (null !== $pictureFile) {
            $this->fileManager->upload($sporting, $pictureFile, 'pictureUrl');
        }
    }
}
