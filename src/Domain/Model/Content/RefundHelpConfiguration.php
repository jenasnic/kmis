<?php

namespace App\Domain\Model\Content;

use App\Enum\RefundHelpEnum;

class RefundHelpConfiguration
{
    public bool $passCitizenEnable = false;

    public ?string $passCitizenLabel = null;

    public ?int $passCitizenAmount = null;

    public ?string $passCitizenHelpText = null;

    public ?string $passCitizenFileLabel = null;

    public bool $passSportEnable = false;

    public ?string $passSportLabel = null;

    public ?int $passSportAmount = null;

    public ?string $passSportHelpText = null;

    public ?string $passSportFileLabel = null;

    public bool $ccasEnable = false;

    public ?string $ccasLabel = null;

    public ?int $ccasAmount = null;

    public ?string $ccasHelpText = null;

    public function getAmount(RefundHelpEnum $refundHelp): ?int
    {
        return match ($refundHelp) {
            RefundHelpEnum::PASS_CITIZEN => $this->passCitizenAmount,
            RefundHelpEnum::PASS_SPORT => $this->passSportAmount,
            RefundHelpEnum::CCAS => $this->ccasAmount,
        };
    }
}
