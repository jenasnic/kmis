<?php

namespace App\Domain\Model\Content;

class RefundHelpConfiguration
{
    public bool $passCitizenEnable = false;

    public ?string $passCitizenLabel = null;

    public ?float $passCitizenAmount = null;

    public ?string $passCitizenHelpText = null;

    public ?string $passCitizenFileLabel = null;

    public bool $passSportEnable = false;

    public ?string $passSportLabel = null;

    public ?float $passSportAmount = null;

    public ?string $passSportHelpText = null;

    public ?string $passSportFileLabel = null;

    public bool $ccasEnable = false;

    public ?string $ccasLabel = null;

    public ?float $ccasAmount = null;

    public ?string $ccasHelpText = null;
}
