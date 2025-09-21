<?php

namespace App\Service\Configuration;

use App\Domain\Model\Content\DiscountConfiguration;
use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;

class DiscountManager
{
    public const PASS_CITIZEN_STATUS = 'PASS_CITIZEN_STATUS';
    public const PASS_CITIZEN_LABEL = 'PASS_CITIZEN_LABEL';
    public const PASS_CITIZEN_HELP_TEXT = 'PASS_CITIZEN_HELP_TEXT';
    public const PASS_SPORT_STATUS = 'PASS_SPORT_STATUS';
    public const PASS_SPORT_LABEL = 'PASS_SPORT_LABEL';
    public const PASS_SPORT_HELP_TEXT = 'PASS_SPORT_HELP_TEXT';
    public const CCAS_STATUS = 'CCAS_STATUS';
    public const CCAS_LABEL = 'CCAS_LABEL';
    public const CCAS_HELP_TEXT = 'CCAS_HELP_TEXT';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ConfigurationRepository $configurationRepository,
    ) {
    }

    public function getDiscountConfiguration(): DiscountConfiguration
    {
        $configurations = $this->configurationRepository->findIndexedByCode([
            self::PASS_CITIZEN_STATUS,
            self::PASS_CITIZEN_LABEL,
            self::PASS_CITIZEN_HELP_TEXT,
            self::PASS_SPORT_STATUS,
            self::PASS_SPORT_LABEL,
            self::PASS_SPORT_HELP_TEXT,
            self::CCAS_STATUS,
            self::CCAS_LABEL,
            self::CCAS_HELP_TEXT,
        ]);

        $discountConfiguration = new DiscountConfiguration();

        $discountConfiguration->enablePassCitizen = $this->isEnabled($configurations[self::PASS_CITIZEN_STATUS] ?? null);
        $discountConfiguration->passCitizenLabel = ($configurations[self::PASS_CITIZEN_LABEL] ?? null)?->getValue();
        $discountConfiguration->passCitizenHelpText = ($configurations[self::PASS_CITIZEN_HELP_TEXT] ?? null)?->getValue();
        $discountConfiguration->enablePassSport = $this->isEnabled($configurations[self::PASS_SPORT_STATUS] ?? null);
        $discountConfiguration->passSportLabel = ($configurations[self::PASS_SPORT_LABEL] ?? null)?->getValue();
        $discountConfiguration->passSportHelpText = ($configurations[self::PASS_SPORT_HELP_TEXT] ?? null)?->getValue();
        $discountConfiguration->enableCCAS = $this->isEnabled($configurations[self::CCAS_STATUS] ?? null);
        $discountConfiguration->CCASLabel = ($configurations[self::CCAS_LABEL] ?? null)?->getValue();
        $discountConfiguration->CCASHelpText = ($configurations[self::CCAS_HELP_TEXT] ?? null)?->getValue();

        return $discountConfiguration;
    }

    public function saveDiscountConfiguration(DiscountConfiguration $discountConfiguration): void
    {
        $this->setEnabled(self::PASS_CITIZEN_STATUS, $discountConfiguration->enablePassCitizen);
        $this->setText(self::PASS_CITIZEN_LABEL, $discountConfiguration->passCitizenLabel);
        $this->setText(self::PASS_CITIZEN_HELP_TEXT, $discountConfiguration->passCitizenHelpText);
        $this->setEnabled(self::PASS_SPORT_STATUS, $discountConfiguration->enablePassSport);
        $this->setText(self::PASS_SPORT_LABEL, $discountConfiguration->passSportLabel);
        $this->setText(self::PASS_SPORT_HELP_TEXT, $discountConfiguration->passSportHelpText);
        $this->setEnabled(self::CCAS_STATUS, $discountConfiguration->enableCCAS);
        $this->setText(self::CCAS_LABEL, $discountConfiguration->CCASLabel);
        $this->setText(self::CCAS_HELP_TEXT, $discountConfiguration->CCASHelpText);

        $this->entityManager->flush();
    }

    private function isEnabled(?Configuration $configuration): bool
    {
        if (null === $configuration) {
            return false;
        }

        return 'ENABLED' === $configuration->getValue();
    }

    private function setEnabled(string $code, bool $enable): void
    {
        $configuration = $this->configurationRepository->getOrCreate($code);
        $configuration->setValue($enable ? 'ENABLED' : 'DISABLED');

        $this->entityManager->persist($configuration);
    }

    private function setText(string $code, ?string $text): void
    {
        $configuration = $this->configurationRepository->getOrCreate($code);
        $configuration->setValue($text);

        $this->entityManager->persist($configuration);
    }
}
