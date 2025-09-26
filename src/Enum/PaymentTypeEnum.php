<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum PaymentTypeEnum: string implements TranslatableEnumInterface
{
    case ANCV = 'ANCV';
    case CASH = 'CASH';
    case CHECK = 'CHECK';
    case DISCOUNT = 'DISCOUNT';
    case HELLO_ASSO = 'HELLO_ASSO';
    case REFUND_HELP = 'REFUND_HELP';
    case TRANSFER = 'TRANSFER';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getTranslationKey());
    }

    public function getTranslationKey(): string
    {
        return 'enum.paymentType.'.$this->name;
    }
}
