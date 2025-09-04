<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum DayOfWeekEnum: string implements TranslatableEnumInterface
{
    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getTranslationKey());
    }

    public function getTranslationKey(): string
    {
        return 'enum.dayOfWeek.'.$this->name;
    }

    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';
}
