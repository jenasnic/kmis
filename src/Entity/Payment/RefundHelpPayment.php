<?php

namespace App\Entity\Payment;

use App\Enum\PaymentTypeEnum;
use App\Enum\RefundHelpEnum;
use App\Repository\Payment\RefundHelpPaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RefundHelpPaymentRepository::class)]
#[ORM\Table(name: 'payment_refund_help')]
class RefundHelpPayment extends AbstractPayment
{
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $reference = null;

    #[ORM\Column(type: Types::STRING, length: 55, enumType: RefundHelpEnum::class)]
    private ?RefundHelpEnum $refundHelp = null;

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getRefundHelp(): ?RefundHelpEnum
    {
        return $this->refundHelp;
    }

    public function setRefundHelp(?RefundHelpEnum $refundHelp): self
    {
        $this->refundHelp = $refundHelp;

        return $this;
    }

    public function getPaymentType(): PaymentTypeEnum
    {
        return PaymentTypeEnum::REFUND_HELP;
    }
}
