<?php

namespace App\Domain\Model\Content;

class RefundHelpConfiguration
{
    public bool $passCitizenEnable = false;

    public ?int $passCitizenAmount = null;

    public ?string $passCitizenHelpText = null;

    public bool $passSportEnable = false;

    public ?int $passSportAmount = null;

    public ?string $passSportHelpText = null;

    public bool $ccasEnable = false;

    public ?int $ccasAmount = null;

    public ?string $ccasHelpText = null;
}
