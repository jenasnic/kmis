<?php

namespace App\Service\Subscriber;

use App\Entity\Adherent;
use App\Entity\Content\News;
use App\Entity\Content\Sporting;
use App\Entity\Registration;
use App\Service\File\FileManager;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;

#[AsDoctrineListener(event: Events::preRemove)]
#[AsDoctrineListener(event: Events::postRemove)]
class RemoveFileSubscriber
{
    /**
     * @var array<string|null>
     */
    private array $filesToRemove = [];

    public function __construct(
        private readonly FileManager $fileManager,
    ) {
    }

    /**
     * @param LifecycleEventArgs<ObjectManager> $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        $this->handleRemovedEntity($object);

        if ($object instanceof Adherent && null !== $object->getRegistration()) {
            $this->handleRemovedEntity($object->getRegistration());
        }
    }

    /**
     * @param LifecycleEventArgs<ObjectManager> $args
     */
    public function postRemove(LifecycleEventArgs $args): void
    {
        foreach ($this->filesToRemove as $filePath) {
            if (!empty($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    protected function handleRemovedEntity(object $object): void
    {
        if ($object instanceof Adherent && null !== $object->getPictureUrl()) {
            $this->filesToRemove[] = $this->fileManager->resolvePath($object, 'pictureUrl');
        }

        if ($object instanceof News && null !== $object->getPictureUrl()) {
            $this->filesToRemove[] = $this->fileManager->resolvePath($object, 'pictureUrl');
        }

        if ($object instanceof Registration) {
            if (null !== $object->getMedicalCertificateUrl()) {
                $this->filesToRemove[] = $this->fileManager->resolvePath($object, 'medicalCertificateUrl');
            }
            if (null !== $object->getLicenceFormUrl()) {
                $this->filesToRemove[] = $this->fileManager->resolvePath($object, 'licenceFormUrl');
            }
            if (null !== $object->getPassCitizenUrl()) {
                $this->filesToRemove[] = $this->fileManager->resolvePath($object, 'passCitizenUrl');
            }
            if (null !== $object->getPassSportUrl()) {
                $this->filesToRemove[] = $this->fileManager->resolvePath($object, 'passSportUrl');
            }
        }

        if ($object instanceof Sporting && null !== $object->getPictureUrl()) {
            $this->filesToRemove[] = $this->fileManager->resolvePath($object, 'pictureUrl');
        }
    }
}
