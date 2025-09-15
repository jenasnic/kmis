<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum RegistrationTypeEnum: string implements TranslatableEnumInterface
{
    case ADULT = 'ADULT';
    case COMPETITOR = 'COMPETITOR';
    case MINOR = 'MINOR';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getTranslationKey());
    }

    public function getTranslationKey(): string
    {
        return 'enum.registrationType.'.$this->name;
    }
}
