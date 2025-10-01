<?php

namespace App\Service\Provider;

use App\Enum\GenderEnum;
use App\Enum\PaymentTypeEnum;
use App\Repository\Payment\PaymentRepository;
use App\Repository\RegistrationRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class StatisticsProvider
{
    public function __construct(
        private readonly RegistrationRepository $registrationRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @return array{
     *     toPay: float,
     *     paid: float,
     *     paymentDistribution: array<array{type: string, count: int, amount: float}>
     * }
     */
    public function getReceipt(int $seasonId): array
    {
        $data = $this->paymentRepository->getDistributionByType($seasonId);

        // NOTE: add label matching type (i.e. translated enum value)
        $updatedData = array_map(fn (array $item) => [
            'type' => $item['type'],
            'label' => PaymentTypeEnum::tryFrom($item['type'])?->trans($this->translator),
            'count' => $item['count'],
            'amount' => $item['amount'],
        ], $data);

        return [
            'toPay' => $this->registrationRepository->getReceiptForSeason($seasonId),
            'paid' => $this->paymentRepository->getReceiptForSeason($seasonId),
            'paymentDistribution' => $updatedData,
        ];
    }

    /**
     * @return array{
     *     genderDistribution: array<array{gender: GenderEnum, count: int}>,
     *     pricingOptionDistribution: array<array{priceOption: string, count: int}>
     * }
     */
    public function getRegistration(int $seasonId): array
    {
        return [
            'genderDistribution' => $this->registrationRepository->getDistributionByGender($seasonId),
            'pricingOptionDistribution' => $this->registrationRepository->getDistributionByPricingOption($seasonId),
        ];
    }
}
