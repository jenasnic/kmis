<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum PaymentTypeEnum: string implements TranslatableEnumInterface
{
    case ANCV = 'ancv';
    case CASH = 'cash';
    case CHECK = 'check';
    case DISCOUNT = 'discount';
    case HELLO_ASSO = 'hello_asso';
    case REFUND_HELP = 'refund_help';
    case TRANSFER = 'transfer';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getTranslationKey());
    }

    public function getTranslationKey(): string
    {
        return 'enum.paymentType.'.$this->name;
    }
}
