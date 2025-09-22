<?php

namespace App\Service\Configuration;

use App\Domain\Model\Content\RefundHelpConfiguration;
use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;

class RefundHelpManager
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

    public function getRefundHelpConfiguration(): RefundHelpConfiguration
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

        $refundHelpConfiguration = new RefundHelpConfiguration();

        $refundHelpConfiguration->passCitizenEnable = $this->isEnabled($configurations[self::PASS_CITIZEN_ENABLE] ?? null);
        $refundHelpConfiguration->passCitizenLabel = ($configurations[self::PASS_CITIZEN_LABEL] ?? null)?->getValue();
        $refundHelpConfiguration->passCitizenHelpText = ($configurations[self::PASS_CITIZEN_HELP_TEXT] ?? null)?->getValue();
        $refundHelpConfiguration->passCitizenFileLabel = ($configurations[self::PASS_CITIZEN_FILE_LABEL] ?? null)?->getValue();
        $refundHelpConfiguration->passSportEnable = $this->isEnabled($configurations[self::PASS_SPORT_ENABLE] ?? null);
        $refundHelpConfiguration->passSportLabel = ($configurations[self::PASS_SPORT_LABEL] ?? null)?->getValue();
        $refundHelpConfiguration->passSportHelpText = ($configurations[self::PASS_SPORT_HELP_TEXT] ?? null)?->getValue();
        $refundHelpConfiguration->passSportFileLabel = ($configurations[self::PASS_SPORT_FILE_LABEL] ?? null)?->getValue();
        $refundHelpConfiguration->ccasEnable = $this->isEnabled($configurations[self::CCAS_ENABLE] ?? null);
        $refundHelpConfiguration->ccasLabel = ($configurations[self::CCAS_LABEL] ?? null)?->getValue();
        $refundHelpConfiguration->ccasHelpText = ($configurations[self::CCAS_HELP_TEXT] ?? null)?->getValue();

        return $refundHelpConfiguration;
    }

    public function saveRefundHelpConfiguration(RefundHelpConfiguration $refundHelpConfiguration): void
    {
        $this->setEnabled(self::PASS_CITIZEN_ENABLE, $refundHelpConfiguration->passCitizenEnable);
        $this->setText(self::PASS_CITIZEN_LABEL, $refundHelpConfiguration->passCitizenLabel);
        $this->setText(self::PASS_CITIZEN_HELP_TEXT, $refundHelpConfiguration->passCitizenHelpText);
        $this->setText(self::PASS_CITIZEN_FILE_LABEL, $refundHelpConfiguration->passCitizenFileLabel);
        $this->setEnabled(self::PASS_SPORT_ENABLE, $refundHelpConfiguration->passSportEnable);
        $this->setText(self::PASS_SPORT_LABEL, $refundHelpConfiguration->passSportLabel);
        $this->setText(self::PASS_SPORT_HELP_TEXT, $refundHelpConfiguration->passSportHelpText);
        $this->setText(self::PASS_SPORT_FILE_LABEL, $refundHelpConfiguration->passSportFileLabel);
        $this->setEnabled(self::CCAS_ENABLE, $refundHelpConfiguration->ccasEnable);
        $this->setText(self::CCAS_LABEL, $refundHelpConfiguration->ccasLabel);
        $this->setText(self::CCAS_HELP_TEXT, $refundHelpConfiguration->ccasHelpText);

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
