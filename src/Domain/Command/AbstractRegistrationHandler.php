<?php

namespace App\Domain\Command;

use App\Entity\Registration;
use App\Service\File\FileManager;

class AbstractRegistrationHandler
{
    public function __construct(
        private readonly FileManager $fileManager,
    ) {
    }

    protected function processUpload(Registration $registration): void
    {
        if (null !== $registration->getAdherent()->getPictureFile()) {
            $this->fileManager->upload($registration->getAdherent(), $registration->getAdherent()->getPictureFile(), 'pictureUrl');
        }

        if (null !== $registration->getMedicalCertificateFile()) {
            $this->fileManager->upload($registration, $registration->getMedicalCertificateFile(), 'medicalCertificateUrl');
        }

        if (null !== $registration->getLicenceFormFile()) {
            $this->fileManager->upload($registration, $registration->getLicenceFormFile(), 'licenceFormUrl');
        }

        // @todo : check if usePassCitizen is true?
        if (null !== $registration->getPassCitizenFile()) {
            $this->fileManager->upload($registration, $registration->getPassCitizenFile(), 'passCitizenUrl');
        }

        // @todo : check if usePassSport is true?
        if (null !== $registration->getPassSportFile()) {
            $this->fileManager->upload($registration, $registration->getPassSportFile(), 'passSportUrl');
        }
    }
}
