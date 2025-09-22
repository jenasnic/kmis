<?php

namespace App\Domain\Model\Content;

class RefundHelpConfiguration
{
    public bool $passCitizenEnable = false;

    public ?string $passCitizenLabel = null;

    public ?string $passCitizenHelpText = null;

    public ?string $passCitizenFileLabel = null;

    public bool $passSportEnable = false;

    public ?string $passSportLabel = null;

    public ?string $passSportHelpText = null;

    public ?string $passSportFileLabel = null;

    public bool $ccasEnable = false;

    public ?string $ccasLabel = null;

    public ?string $ccasHelpText = null;
}
