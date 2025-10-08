<?php

namespace App\Controller\Back;

use App\Entity\Adherent;
use App\Entity\Registration;
use App\Service\File\FileManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FileController extends AbstractController
{
    private AsciiSlugger $slugger;

    public function __construct(
        private readonly FileManager $fileManager,
    ) {
        $this->slugger = new AsciiSlugger('fr_FR');
    }

    #[Route('/telecharger-attestation/{registration}', name: 'bo_download_attestation', methods: ['GET'])]
    public function medicalCertificate(Registration $registration): Response
    {
        $file = $this->fileManager->download($registration, 'medicalCertificateUrl');
        if (null === $file) {
            throw new \LogicException('invalid file');
        }

        $fileName = $this->buildFileName($registration->getAdherent(), $registration->getMedicalCertificateUrl(), 'certificat');

        return $this->getFileContent($file, $fileName);
    }

    #[Route('/telecharger-formulaire-licence/{registration}', name: 'bo_download_licence_form', methods: ['GET'])]
    public function licenceForm(Registration $registration): Response
    {
        $file = $this->fileManager->download($registration, 'licenceFormUrl');
        if (null === $file) {
            throw new \LogicException('invalid file');
        }

        $fileName = $this->buildFileName($registration->getAdherent(), $registration->getLicenceFormUrl(), 'licence');

        return $this->getFileContent($file, $fileName);
    }

    #[Route('/telecharger-pass-citizen/{registration}', name: 'bo_download_pass_citizen', methods: ['GET'])]
    public function passCitizen(Registration $registration): Response
    {
        $file = $this->fileManager->download($registration, 'passCitizenUrl');
        if (null === $file) {
            throw new \LogicException('invalid file');
        }

        $fileName = $this->buildFileName($registration->getAdherent(), $registration->getPassCitizenUrl(), 'pass_citizen');

        return $this->getFileContent($file, $fileName);
    }

    #[Route('/telecharger-pass-sport/{registration}', name: 'bo_download_pass_sport', methods: ['GET'])]
    public function passSport(Registration $registration): Response
    {
        $file = $this->fileManager->download($registration, 'passSportUrl');
        if (null === $file) {
            throw new \LogicException('invalid file');
        }

        $fileName = $this->buildFileName($registration->getAdherent(), $registration->getPassSportUrl(), 'pass_sport');

        return $this->getFileContent($file, $fileName);
    }

    #[Route('/telecharger-photo/{adherent}', name: 'bo_download_picture', methods: ['GET'])]
    public function picture(Adherent $adherent): Response
    {
        $file = $this->fileManager->download($adherent, 'pictureUrl');
        if (null === $file) {
            throw new \LogicException('invalid file');
        }

        $fileName = $this->buildFileName($adherent, $adherent->getPictureUrl(), 'photo');

        return $this->getFileContent($file, $fileName);
    }

    private function getFileContent(\SplFileInfo $file, string $fileName): Response
    {
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $fileName);

        return $response;
    }

    private function buildFileName(Adherent $adherent, ?string $baseFileName, string $suffix): string
    {
        /** @var string $firstName */
        $firstName = $adherent->getFirstName();
        /** @var string $lastName */
        $lastName = $adherent->getLastName();

        $fileName = strtolower(sprintf(
            '%s_%s_%s',
            $this->slugger->slug($lastName),
            $this->slugger->slug($firstName),
            $suffix,
        ));

        if (null === $baseFileName) {
            return $fileName;
        }

        $pathInfo = pathinfo($baseFileName);

        if (isset($pathInfo['extension'])) {
            $fileName = sprintf('%s.%s', $fileName, $pathInfo['extension']);
        }

        return $fileName;
    }
}
