<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

interface TranslatableEnumInterface extends TranslatableInterface
{
    public function getTranslationKey(): string;
}
