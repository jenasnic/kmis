<?php

namespace App\Service\Configuration;

use App\Domain\Model\Content\DiscountConfiguration;
use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;

class DiscountManager
{
    public const PASS_CITIZEN_ENABLE = 'PASS_CITIZEN_ENABLE';
    public const PASS_CITIZEN_LABEL = 'PASS_CITIZEN_LABEL';
    public const PASS_CITIZEN_HELP_TEXT = 'PASS_CITIZEN_HELP_TEXT';
    public const PASS_CITIZEN_FILE_LABEL = 'PASS_CITIZEN_FILE_LABEL';
    public const PASS_SPORT_ENABLE = 'PASS_SPORT_ENABLE';
    public const PASS_SPORT_LABEL = 'PASS_SPORT_LABEL';
    public const PASS_SPORT_HELP_TEXT = 'PASS_SPORT_HELP_TEXT';
    public const PASS_SPORT_FILE_LABEL = 'PASS_SPORT_FILE_LABEL';
    public const CCAS_ENABLE = 'CCAS_ENABLE';
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
            self::PASS_CITIZEN_ENABLE,
            self::PASS_CITIZEN_LABEL,
            self::PASS_CITIZEN_HELP_TEXT,
            self::PASS_CITIZEN_FILE_LABEL,
            self::PASS_SPORT_ENABLE,
            self::PASS_SPORT_LABEL,
            self::PASS_SPORT_HELP_TEXT,
            self::PASS_SPORT_FILE_LABEL,
            self::CCAS_ENABLE,
            self::CCAS_LABEL,
            self::CCAS_HELP_TEXT,
        ]);

        $discountConfiguration = new DiscountConfiguration();

        $discountConfiguration->passCitizenEnable = $this->isEnabled($configurations[self::PASS_CITIZEN_ENABLE] ?? null);
        $discountConfiguration->passCitizenLabel = ($configurations[self::PASS_CITIZEN_LABEL] ?? null)?->getValue();
        $discountConfiguration->passCitizenHelpText = ($configurations[self::PASS_CITIZEN_HELP_TEXT] ?? null)?->getValue();
        $discountConfiguration->passCitizenFileLabel = ($configurations[self::PASS_CITIZEN_FILE_LABEL] ?? null)?->getValue();
        $discountConfiguration->passSportEnable = $this->isEnabled($configurations[self::PASS_SPORT_ENABLE] ?? null);
        $discountConfiguration->passSportLabel = ($configurations[self::PASS_SPORT_LABEL] ?? null)?->getValue();
        $discountConfiguration->passSportHelpText = ($configurations[self::PASS_SPORT_HELP_TEXT] ?? null)?->getValue();
        $discountConfiguration->passSportFileLabel = ($configurations[self::PASS_SPORT_FILE_LABEL] ?? null)?->getValue();
        $discountConfiguration->ccasEnable = $this->isEnabled($configurations[self::CCAS_ENABLE] ?? null);
        $discountConfiguration->ccasLabel = ($configurations[self::CCAS_LABEL] ?? null)?->getValue();
        $discountConfiguration->ccasHelpText = ($configurations[self::CCAS_HELP_TEXT] ?? null)?->getValue();

        return $discountConfiguration;
    }

    public function saveDiscountConfiguration(DiscountConfiguration $discountConfiguration): void
    {
        $this->setEnabled(self::PASS_CITIZEN_ENABLE, $discountConfiguration->passCitizenEnable);
        $this->setText(self::PASS_CITIZEN_LABEL, $discountConfiguration->passCitizenLabel);
        $this->setText(self::PASS_CITIZEN_HELP_TEXT, $discountConfiguration->passCitizenHelpText);
        $this->setText(self::PASS_CITIZEN_FILE_LABEL, $discountConfiguration->passCitizenFileLabel);
        $this->setEnabled(self::PASS_SPORT_ENABLE, $discountConfiguration->passSportEnable);
        $this->setText(self::PASS_SPORT_LABEL, $discountConfiguration->passSportLabel);
        $this->setText(self::PASS_SPORT_HELP_TEXT, $discountConfiguration->passSportHelpText);
        $this->setText(self::PASS_SPORT_FILE_LABEL, $discountConfiguration->passSportFileLabel);
        $this->setEnabled(self::CCAS_ENABLE, $discountConfiguration->ccasEnable);
        $this->setText(self::CCAS_LABEL, $discountConfiguration->ccasLabel);
        $this->setText(self::CCAS_HELP_TEXT, $discountConfiguration->ccasHelpText);

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
