<?php

namespace App\Service\Configuration;

use App\Entity\Payment\DiscountCode;
use App\Entity\Registration;
use App\Enum\RefundHelpEnum;
use App\Repository\Payment\DiscountCodeRepository;

class DiscountManager
{
    public function __construct(
        private readonly DiscountCodeRepository $discountCodeRepository,
        private readonly RefundHelpManager $refundHelpManager,
    ) {
    }

    public function getDiscountCode(Registration $registration): ?DiscountCode
    {
        $refundHelps = $registration->getRefundHelps();
        $refundHelps = array_filter($refundHelps, fn (RefundHelpEnum $refundHelp) => $this->refundHelpManager->isEnable($refundHelp));

        $discountCodes = $this->discountCodeRepository->findAll();

        foreach ($discountCodes as $discountCode) {
            if ($discountCode->matchRefundHelps($refundHelps)) {
                return $discountCode;
            }
        }

        return null;
    }

    public function getDiscountAmount(DiscountCode $discountCode): int
    {
        $amount = 0;
        foreach ($discountCode->getRefundHelps() as $refundHelp) {
            $amount += $this->refundHelpManager->getAmount($refundHelp);
        }

        return $amount;
    }
}
