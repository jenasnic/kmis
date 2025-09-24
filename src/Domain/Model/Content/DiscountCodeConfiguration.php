<?php

namespace App\Domain\Model\Content;

use App\Entity\Payment\DiscountCode;

class DiscountCodeConfiguration
{
    /**
     * @var array<DiscountCode>
     */
    public array $discountCodes;

    /**
     * @param array<DiscountCode> $discountCodes
     */
    public function __construct(array $discountCodes)
    {
        $this->discountCodes = $discountCodes;
    }

    /**
     * @return array<int>
     */
    public function getDiscountCodeIds(): array
    {
        /** @var array<int> */
        return array_map(fn (DiscountCode $discountCode) => $discountCode->getId(), $this->discountCodes);
    }
}
