<?php

namespace App\Domain\Command\Back\Content;

use App\Entity\Content\Sporting;
use App\Enum\FileTypeEnum;
use App\Repository\Content\SportingRepository;
use App\Service\File\FileCleaner;
use App\Service\File\FileUploader;

final class SaveSportingHandler
{
    public function __construct(
        private readonly SportingRepository $sportingRepository,
        private readonly FileUploader $fileUploader,
        private readonly FileCleaner $fileCleaner,
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
            $this->fileCleaner->cleanEntity($sporting, FileTypeEnum::PICTURE);
            $sporting->setPictureUrl($this->fileUploader->upload($pictureFile, Sporting::PICTURE_FOLDER));
        }
    }
}
