<?php

namespace App\Domain\Command\Back;

use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use App\Service\File\FileManager;

final class SaveAdherentHandler
{
    public function __construct(
        private readonly AdherentRepository $adherentRepository,
        private readonly FileManager $fileManager,
    ) {
    }

    public function handle(SaveAdherentCommand $command): void
    {
        $this->processFile($command->adherent);

        $this->adherentRepository->add($command->adherent, true);
    }

    private function processFile(Adherent $adherent): void
    {
        $pictureFile = $adherent->getPictureFile();
        if (null !== $pictureFile) {
            $this->fileManager->upload($adherent, $pictureFile, 'pictureUrl');
        }
    }
}
