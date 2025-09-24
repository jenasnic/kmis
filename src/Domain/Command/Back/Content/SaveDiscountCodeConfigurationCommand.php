<?php

namespace App\Domain\Command\Back\Content;

use App\Domain\Model\Content\DiscountCodeConfiguration;

class SaveDiscountCodeConfigurationCommand
{
    public function __construct(
        public DiscountCodeConfiguration $discountCodeConfiguration,
    ) {
    }
}
