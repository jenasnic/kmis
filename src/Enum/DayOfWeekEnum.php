<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum DayOfWeekEnum: int implements TranslatableEnumInterface
{
    case MONDAY = 0;
    case TUESDAY = 1;
    case WEDNESDAY = 2;
    case THURSDAY = 3;
    case FRIDAY = 4;
    case SATURDAY = 5;
    case SUNDAY = 6;

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getTranslationKey());
    }

    public function getTranslationKey(): string
    {
        return 'enum.dayOfWeek.'.$this->name;
    }
}
