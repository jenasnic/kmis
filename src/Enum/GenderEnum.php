<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum GenderEnum: string implements TranslatableEnumInterface
{
    case MALE = 'MALE';
    case FEMALE = 'FEMALE';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getTranslationKey());
    }

    public function getTranslationKey(): string
    {
        return 'enum.gender.'.$this->name;
    }
}
