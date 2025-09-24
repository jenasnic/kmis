<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum RefundHelpEnum: string implements TranslatableEnumInterface
{
    case PASS_CITIZEN = 'PASS_CITIZEN';
    case PASS_SPORT = 'PASS_SPORT';
    case CCAS = 'CCAS';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getTranslationKey());
    }

    public function getTranslationKey(): string
    {
        return 'enum.refundHelp.'.$this->name;
    }
}
