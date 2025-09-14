<?php

namespace App\Domain\Command\Back;

use App\Domain\Command\AbstractRegistrationHandler;
use App\Repository\RegistrationRepository;
use App\Service\File\FileManager;

final class NewRegistrationHandler extends AbstractRegistrationHandler
{
    public function __construct(
        FileManager $fileManager,
        private readonly RegistrationRepository $registrationRepository,
    ) {
        parent::__construct($fileManager);
    }

    public function handle(NewRegistrationCommand $command): void
    {
        $registration = $command->registration;

        $this->processUpload($registration);

        $this->registrationRepository->add($registration, true);
    }
}
