<?php

namespace App\Service\Configuration;

use App\Domain\Model\Content\RefundHelpConfiguration;
use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;

class RefundHelpManager
{
    public const REFUND_HELP_PASS_CITIZEN_ENABLE = 'REFUND_HELP_PASS_CITIZEN_ENABLE';
    public const REFUND_HELP_PASS_CITIZEN_LABEL = 'REFUND_HELP_PASS_CITIZEN_LABEL';
    public const REFUND_HELP_PASS_CITIZEN_AMOUNT = 'REFUND_HELP_PASS_CITIZEN_AMOUNT';
    public const REFUND_HELP_PASS_CITIZEN_HELP_TEXT = 'REFUND_HELP_PASS_CITIZEN_HELP_TEXT';
    public const REFUND_HELP_PASS_CITIZEN_FILE_LABEL = 'REFUND_HELP_PASS_CITIZEN_FILE_LABEL';
    public const REFUND_HELP_PASS_SPORT_ENABLE = 'REFUND_HELP_PASS_SPORT_ENABLE';
    public const REFUND_HELP_PASS_SPORT_LABEL = 'REFUND_HELP_PASS_SPORT_LABEL';
    public const REFUND_HELP_PASS_SPORT_AMOUNT = 'REFUND_HELP_PASS_SPORT_AMOUNT';
    public const REFUND_HELP_PASS_SPORT_HELP_TEXT = 'REFUND_HELP_PASS_SPORT_HELP_TEXT';
    public const REFUND_HELP_PASS_SPORT_FILE_LABEL = 'REFUND_HELP_PASS_SPORT_FILE_LABEL';
    public const REFUND_HELP_CCAS_ENABLE = 'REFUND_HELP_CCAS_ENABLE';
    public const REFUND_HELP_CCAS_LABEL = 'REFUND_HELP_CCAS_LABEL';
    public const REFUND_HELP_CCAS_AMOUNT = 'REFUND_HELP_CCAS_AMOUNT';
    public const REFUND_HELP_CCAS_HELP_TEXT = 'REFUND_HELP_CCAS_HELP_TEXT';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ConfigurationRepository $configurationRepository,
    ) {
    }

    public function getRefundHelpConfiguration(): RefundHelpConfiguration
    {
        $configurations = $this->configurationRepository->findIndexedByCode([
            self::REFUND_HELP_PASS_CITIZEN_ENABLE,
            self::REFUND_HELP_PASS_CITIZEN_LABEL,
            self::REFUND_HELP_PASS_CITIZEN_AMOUNT,
            self::REFUND_HELP_PASS_CITIZEN_HELP_TEXT,
            self::REFUND_HELP_PASS_CITIZEN_FILE_LABEL,
            self::REFUND_HELP_PASS_SPORT_ENABLE,
            self::REFUND_HELP_PASS_SPORT_LABEL,
            self::REFUND_HELP_PASS_SPORT_AMOUNT,
            self::REFUND_HELP_PASS_SPORT_HELP_TEXT,
            self::REFUND_HELP_PASS_SPORT_FILE_LABEL,
            self::REFUND_HELP_CCAS_ENABLE,
            self::REFUND_HELP_CCAS_LABEL,
            self::REFUND_HELP_CCAS_AMOUNT,
            self::REFUND_HELP_CCAS_HELP_TEXT,
        ]);

        $refundHelpConfiguration = new RefundHelpConfiguration();

        $refundHelpConfiguration->passCitizenEnable = $this->isEnabled($configurations[self::REFUND_HELP_PASS_CITIZEN_ENABLE] ?? null);
        $refundHelpConfiguration->passCitizenLabel = ($configurations[self::REFUND_HELP_PASS_CITIZEN_LABEL] ?? null)?->getValue();
        $refundHelpConfiguration->passCitizenAmount = floatval(($configurations[self::REFUND_HELP_PASS_CITIZEN_AMOUNT] ?? null)?->getValue());
        $refundHelpConfiguration->passCitizenHelpText = ($configurations[self::REFUND_HELP_PASS_CITIZEN_HELP_TEXT] ?? null)?->getValue();
        $refundHelpConfiguration->passCitizenFileLabel = ($configurations[self::REFUND_HELP_PASS_CITIZEN_FILE_LABEL] ?? null)?->getValue();
        $refundHelpConfiguration->passSportEnable = $this->isEnabled($configurations[self::REFUND_HELP_PASS_SPORT_ENABLE] ?? null);
        $refundHelpConfiguration->passSportLabel = ($configurations[self::REFUND_HELP_PASS_SPORT_LABEL] ?? null)?->getValue();
        $refundHelpConfiguration->passSportAmount = floatval(($configurations[self::REFUND_HELP_PASS_SPORT_AMOUNT] ?? null)?->getValue());
        $refundHelpConfiguration->passSportHelpText = ($configurations[self::REFUND_HELP_PASS_SPORT_HELP_TEXT] ?? null)?->getValue();
        $refundHelpConfiguration->passSportFileLabel = ($configurations[self::REFUND_HELP_PASS_SPORT_FILE_LABEL] ?? null)?->getValue();
        $refundHelpConfiguration->ccasEnable = $this->isEnabled($configurations[self::REFUND_HELP_CCAS_ENABLE] ?? null);
        $refundHelpConfiguration->ccasLabel = ($configurations[self::REFUND_HELP_CCAS_LABEL] ?? null)?->getValue();
        $refundHelpConfiguration->ccasAmount = floatval(($configurations[self::REFUND_HELP_CCAS_AMOUNT] ?? null)?->getValue());
        $refundHelpConfiguration->ccasHelpText = ($configurations[self::REFUND_HELP_CCAS_HELP_TEXT] ?? null)?->getValue();

        return $refundHelpConfiguration;
    }

    public function saveRefundHelpConfiguration(RefundHelpConfiguration $refundHelpConfiguration): void
    {
        $this->setEnabled(self::REFUND_HELP_PASS_CITIZEN_ENABLE, $refundHelpConfiguration->passCitizenEnable);
        $this->setText(self::REFUND_HELP_PASS_CITIZEN_LABEL, $refundHelpConfiguration->passCitizenLabel);
        $this->setText(self::REFUND_HELP_PASS_CITIZEN_AMOUNT, (string) $refundHelpConfiguration->passCitizenAmount);
        $this->setText(self::REFUND_HELP_PASS_CITIZEN_HELP_TEXT, $refundHelpConfiguration->passCitizenHelpText);
        $this->setText(self::REFUND_HELP_PASS_CITIZEN_FILE_LABEL, $refundHelpConfiguration->passCitizenFileLabel);
        $this->setEnabled(self::REFUND_HELP_PASS_SPORT_ENABLE, $refundHelpConfiguration->passSportEnable);
        $this->setText(self::REFUND_HELP_PASS_SPORT_LABEL, $refundHelpConfiguration->passSportLabel);
        $this->setText(self::REFUND_HELP_PASS_SPORT_AMOUNT, (string) $refundHelpConfiguration->passSportAmount);
        $this->setText(self::REFUND_HELP_PASS_SPORT_HELP_TEXT, $refundHelpConfiguration->passSportHelpText);
        $this->setText(self::REFUND_HELP_PASS_SPORT_FILE_LABEL, $refundHelpConfiguration->passSportFileLabel);
        $this->setEnabled(self::REFUND_HELP_CCAS_ENABLE, $refundHelpConfiguration->ccasEnable);
        $this->setText(self::REFUND_HELP_CCAS_LABEL, $refundHelpConfiguration->ccasLabel);
        $this->setText(self::REFUND_HELP_CCAS_AMOUNT, (string) $refundHelpConfiguration->ccasAmount);
        $this->setText(self::REFUND_HELP_CCAS_HELP_TEXT, $refundHelpConfiguration->ccasHelpText);

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
