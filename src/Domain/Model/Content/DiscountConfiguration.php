<?php

namespace App\Domain\Model\Content;

class DiscountConfiguration
{
    public bool $enablePassCitizen = false;

    public ?string $passCitizenLabel = null;

    public ?string $passCitizenHelpText = null;

    public bool $enablePassSport = false;

    public ?string $passSportLabel = null;

    public ?string $passSportHelpText = null;

    public bool $enableCCAS = false;

    public ?string $CCASLabel = null;

    public ?string $CCASHelpText = null;
}
