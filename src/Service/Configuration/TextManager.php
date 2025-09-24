<?php

namespace App\Service\Configuration;

use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;

class TextManager
{
    public const TEXT_HOME_PRESENTATION = 'TEXT_HOME_PRESENTATION';
    public const TEXT_CONTACT = 'TEXT_CONTACT';

    public function __construct(
        private readonly ConfigurationRepository $configurationRepository,
    ) {
    }

    public function getHomePresentation(): ?string
    {
        /** @var Configuration|null $configuration */
        $configuration = $this->configurationRepository->find(self::TEXT_HOME_PRESENTATION);

        return $configuration?->getValue();
    }

    public function getContact(): ?string
    {
        /** @var Configuration|null $configuration */
        $configuration = $this->configurationRepository->find(self::TEXT_CONTACT);

        return $configuration?->getValue();
    }
}
