<?php

namespace App\Service\Configuration;

use App\Domain\Model\Content\RefundHelpConfiguration;
use App\Entity\Configuration;
use App\Entity\Registration;
use App\Enum\RefundHelpEnum;
use App\Repository\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RefundHelpManager
{
    public const REFUND_HELP_PASS_CITIZEN_ENABLE = 'REFUND_HELP_PASS_CITIZEN_ENABLE';
    public const REFUND_HELP_PASS_CITIZEN_AMOUNT = 'REFUND_HELP_PASS_CITIZEN_AMOUNT';
    public const REFUND_HELP_PASS_CITIZEN_HELP_TEXT = 'REFUND_HELP_PASS_CITIZEN_HELP_TEXT';
    public const REFUND_HELP_PASS_SPORT_ENABLE = 'REFUND_HELP_PASS_SPORT_ENABLE';
    public const REFUND_HELP_PASS_SPORT_AMOUNT = 'REFUND_HELP_PASS_SPORT_AMOUNT';
    public const REFUND_HELP_PASS_SPORT_HELP_TEXT = 'REFUND_HELP_PASS_SPORT_HELP_TEXT';
    public const REFUND_HELP_CCAS_ENABLE = 'REFUND_HELP_CCAS_ENABLE';
    public const REFUND_HELP_CCAS_AMOUNT = 'REFUND_HELP_CCAS_AMOUNT';
    public const REFUND_HELP_CCAS_HELP_TEXT = 'REFUND_HELP_CCAS_HELP_TEXT';

    private ?RefundHelpConfiguration $cachedConfiguration = null;

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $entityManager,
        private readonly ConfigurationRepository $configurationRepository,
    ) {
    }

    public function getRefundHelpConfiguration(): RefundHelpConfiguration
    {
        if (null !== $this->cachedConfiguration) {
            return $this->cachedConfiguration;
        }

        $configurations = $this->configurationRepository->findIndexedByCode([
            self::REFUND_HELP_PASS_CITIZEN_ENABLE,
            self::REFUND_HELP_PASS_CITIZEN_AMOUNT,
            self::REFUND_HELP_PASS_CITIZEN_HELP_TEXT,
            self::REFUND_HELP_PASS_SPORT_ENABLE,
            self::REFUND_HELP_PASS_SPORT_AMOUNT,
            self::REFUND_HELP_PASS_SPORT_HELP_TEXT,
            self::REFUND_HELP_CCAS_ENABLE,
            self::REFUND_HELP_CCAS_AMOUNT,
            self::REFUND_HELP_CCAS_HELP_TEXT,
        ]);

        $refundHelpConfiguration = new RefundHelpConfiguration();

        $refundHelpConfiguration->passCitizenEnable = $this->getEnabledValue($configurations[self::REFUND_HELP_PASS_CITIZEN_ENABLE] ?? null);
        $refundHelpConfiguration->passCitizenAmount = intval(($configurations[self::REFUND_HELP_PASS_CITIZEN_AMOUNT] ?? null)?->getValue());
        $refundHelpConfiguration->passCitizenHelpText = ($configurations[self::REFUND_HELP_PASS_CITIZEN_HELP_TEXT] ?? null)?->getValue();
        $refundHelpConfiguration->passSportEnable = $this->getEnabledValue($configurations[self::REFUND_HELP_PASS_SPORT_ENABLE] ?? null);
        $refundHelpConfiguration->passSportAmount = intval(($configurations[self::REFUND_HELP_PASS_SPORT_AMOUNT] ?? null)?->getValue());
        $refundHelpConfiguration->passSportHelpText = ($configurations[self::REFUND_HELP_PASS_SPORT_HELP_TEXT] ?? null)?->getValue();
        $refundHelpConfiguration->ccasEnable = $this->getEnabledValue($configurations[self::REFUND_HELP_CCAS_ENABLE] ?? null);
        $refundHelpConfiguration->ccasAmount = intval(($configurations[self::REFUND_HELP_CCAS_AMOUNT] ?? null)?->getValue());
        $refundHelpConfiguration->ccasHelpText = ($configurations[self::REFUND_HELP_CCAS_HELP_TEXT] ?? null)?->getValue();

        $this->cachedConfiguration = $refundHelpConfiguration;

        return $this->cachedConfiguration;
    }

    public function saveRefundHelpConfiguration(RefundHelpConfiguration $refundHelpConfiguration): void
    {
        $this->setEnabledValue(self::REFUND_HELP_PASS_CITIZEN_ENABLE, $refundHelpConfiguration->passCitizenEnable);
        $this->setTextValue(self::REFUND_HELP_PASS_CITIZEN_AMOUNT, (string) $refundHelpConfiguration->passCitizenAmount);
        $this->setTextValue(self::REFUND_HELP_PASS_CITIZEN_HELP_TEXT, $refundHelpConfiguration->passCitizenHelpText);
        $this->setEnabledValue(self::REFUND_HELP_PASS_SPORT_ENABLE, $refundHelpConfiguration->passSportEnable);
        $this->setTextValue(self::REFUND_HELP_PASS_SPORT_AMOUNT, (string) $refundHelpConfiguration->passSportAmount);
        $this->setTextValue(self::REFUND_HELP_PASS_SPORT_HELP_TEXT, $refundHelpConfiguration->passSportHelpText);
        $this->setEnabledValue(self::REFUND_HELP_CCAS_ENABLE, $refundHelpConfiguration->ccasEnable);
        $this->setTextValue(self::REFUND_HELP_CCAS_AMOUNT, (string) $refundHelpConfiguration->ccasAmount);
        $this->setTextValue(self::REFUND_HELP_CCAS_HELP_TEXT, $refundHelpConfiguration->ccasHelpText);

        $this->entityManager->flush();

        $this->cachedConfiguration = $refundHelpConfiguration;
    }

    public function getAmount(RefundHelpEnum $refundHelp): ?int
    {
        $configuration = $this->getRefundHelpConfiguration();

        return match ($refundHelp) {
            RefundHelpEnum::PASS_CITIZEN => $configuration->passCitizenAmount,
            RefundHelpEnum::PASS_SPORT => $configuration->passSportAmount,
            RefundHelpEnum::CCAS => $configuration->ccasAmount,
        };
    }

    public function getLabel(RefundHelpEnum $refundHelp): string
    {
        $baseLabel = $refundHelp->trans($this->translator);

        $amount = $this->getAmount($refundHelp);

        return empty($amount) ? $baseLabel : sprintf('%s (%d €)', $baseLabel, $amount);
    }

    public function isEnable(RefundHelpEnum $refundHelp): bool
    {
        $configuration = $this->getRefundHelpConfiguration();

        return match ($refundHelp) {
            RefundHelpEnum::PASS_CITIZEN => $configuration->passCitizenEnable,
            RefundHelpEnum::PASS_SPORT => $configuration->passSportEnable,
            RefundHelpEnum::CCAS => $configuration->ccasEnable,
        };
    }

    public function getRefundHelpAmount(Registration $registration): int
    {
        $amount = 0;

        $refundHelps = $registration->getRefundHelps();

        foreach ($refundHelps as $refundHelp) {
            $amount += $this->getAmount($refundHelp);
        }

        return $amount;
    }

    private function getEnabledValue(?Configuration $configuration): bool
    {
        if (null === $configuration) {
            return false;
        }

        return 'ENABLED' === $configuration->getValue();
    }

    private function setEnabledValue(string $code, bool $enable): void
    {
        $configuration = $this->configurationRepository->getOrCreate($code);
        $configuration->setValue($enable ? 'ENABLED' : 'DISABLED');

        $this->entityManager->persist($configuration);
    }

    private function setTextValue(string $code, ?string $text): void
    {
        $configuration = $this->configurationRepository->getOrCreate($code);
        $configuration->setValue($text);

        $this->entityManager->persist($configuration);
    }
}
