<?php

namespace App\Domain\Command\Back;

use App\Entity\Registration;
use App\Repository\RegistrationRepository;
use App\Service\File\FileManager;

final class SaveRegistrationHandler
{
    public function __construct(
        private readonly RegistrationRepository $registrationRepository,
        private readonly FileManager $fileManager,
    ) {
    }

    public function handle(SaveRegistrationCommand $command): void
    {
        $this->processFile($command->registration);

        $this->registrationRepository->add($command->registration, true);
    }

    private function processFile(Registration $registration): void
    {
        $medicalCertificateFile = $registration->getMedicalCertificateFile();
        if (null !== $medicalCertificateFile) {
            $this->fileManager->upload($registration, $medicalCertificateFile, 'medicalCertificateUrl');
        }

        $licenceFormFile = $registration->getLicenceFormFile();
        if (null !== $licenceFormFile) {
            $this->fileManager->upload($registration, $licenceFormFile, 'licenceFormUrl');
        }

        $passCitizenFile = $registration->getPassCitizenFile();
        if (null !== $passCitizenFile) {
            $this->fileManager->upload($registration, $passCitizenFile, 'passCitizenUrl');
        }

        $passSportFile = $registration->getPassSportFile();
        if (null !== $passSportFile) {
            $this->fileManager->upload($registration, $passSportFile, 'passSportUrl');
        }
    }
}
