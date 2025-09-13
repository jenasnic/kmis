<?php

namespace App\Service\Configuration;

use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;

class TextManager
{
    public const HOME_PRESENTATION = 'HOME_PRESENTATION';
    public const CONTACT = 'CONTACT';

    public function __construct(
        private readonly ConfigurationRepository $configurationRepository,
    ) {
    }

    public function getHomePresentation(): ?string
    {
        /** @var Configuration|null $configuration */
        $configuration = $this->configurationRepository->find(self::HOME_PRESENTATION);

        return $configuration?->getValue();
    }

    public function getContact(): ?string
    {
        /** @var Configuration|null $configuration */
        $configuration = $this->configurationRepository->find(self::CONTACT);

        return $configuration?->getValue();
    }
}
