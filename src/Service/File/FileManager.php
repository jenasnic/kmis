<?php

namespace App\Service\File;

use App\Entity\Adherent;
use App\Entity\Content\News;
use App\Entity\Content\Sporting;
use App\Entity\Registration;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class FileManager
{
    public function __construct(
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly string $uploadPath,
    ) {
    }

    public function upload(object $entity, UploadedFile $uploadedFile, string $property): void
    {
        $basePath = $this->getBasePath($entity);

        /** @var string|null $value */
        $value = $this->propertyAccessor->getValue($entity, $property);
        $this->removeFile($basePath, $value);

        $fileName = sprintf(
            '%s.%s',
            str_replace('.', '', uniqid('', true)),
            $uploadedFile->getClientOriginalExtension()
        );

        $uploadedFile->move($basePath, $fileName);

        $this->propertyAccessor->setValue($entity, $property, $fileName);
    }

    public function download(object $entity, string $property): ?\SplFileInfo
    {
        $filePath = $this->resolvePath($entity, $property);
        if (empty($filePath) || !file_exists($filePath)) {
            return null;
        }

        return new \SplFileInfo($filePath);
    }

    public function resolvePath(object $entity, string $property): ?string
    {
        $basePath = $this->getBasePath($entity);

        /** @var string|null $value */
        $value = $this->propertyAccessor->getValue($entity, $property);
        if (empty($value)) {
            return null;
        }

        return $basePath.DIRECTORY_SEPARATOR.$value;
    }

    /**
     * Remove all files from current registration.
     */
    public function cleanRegistration(Registration $registration): void
    {
        $basePath = $this->getBasePath($registration);

        $this->removeFile($basePath, $registration->getMedicalCertificateFile());
        $registration->setMedicalCertificateFile(null);

        $this->removeFile($basePath, $registration->getLicenceFormFile());
        $registration->setLicenceFormFile(null);

        $this->removeFile($basePath, $registration->getPassCitizenFile());
        $registration->setPassCitizenFile(null);

        $this->removeFile($basePath, $registration->getPassSportFile());
        $registration->setPassSportFile(null);
    }

    protected function removeFile(string $basePath, ?string $fileName): void
    {
        if (!empty($fileName)) {
            $filePath = $basePath.DIRECTORY_SEPARATOR.$fileName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    protected function getBasePath(object $entity): string
    {
        $folderName = match (true) {
            $entity instanceof Adherent => Adherent::PICTURE_FOLDER,
            $entity instanceof News => News::PICTURE_FOLDER,
            $entity instanceof Registration => Registration::DOCUMENT_FOLDER,
            $entity instanceof Sporting => Sporting::PICTURE_FOLDER,
            default => throw new \LogicException('unsupported entity type'),
        };

        return $this->uploadPath.$folderName;
    }
}
