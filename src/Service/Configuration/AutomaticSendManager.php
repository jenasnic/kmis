<?php

namespace App\Service\Configuration;

use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;

class AutomaticSendManager
{
    public const AUTOMATIC_SEND = 'AUTOMATIC_SEND';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ConfigurationRepository $configurationRepository,
    ) {
    }

    public function isAutomaticSendEnable(): bool
    {
        /** @var Configuration|null $configuration */
        $configuration = $this->configurationRepository->find(self::AUTOMATIC_SEND);
        if (null === $configuration) {
            return false;
        }

        return match ($configuration->getValue()) {
            'ACTIVE' => true,
            'INACTIVE' => false,
            default => throw new \LogicException('invalid value'),
        };
    }

    public function enableAutomaticSend(): void
    {
        $this->setAutomaticSendState(true);
    }

    public function disableAutomaticSend(): void
    {
        $this->setAutomaticSendState(false);
    }

    protected function setAutomaticSendState(bool $state): void
    {
        $value = $state ? 'ACTIVE' : 'INACTIVE';

        $configuration = $this->configurationRepository->getOrCreate(self::AUTOMATIC_SEND);

        $configuration->setValue($value);

        $this->entityManager->persist($configuration);
        $this->entityManager->flush();
    }
}
