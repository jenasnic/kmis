<?php

namespace App\Entity\Payment;

use App\Enum\PaymentTypeEnum;
use App\Repository\Payment\DiscountPaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DiscountPaymentRepository::class)]
#[ORM\Table(name: 'payment_discount')]
class DiscountPayment extends AbstractPayment
{
    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    private ?string $discount = null;

    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(?string $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getPaymentType(): PaymentTypeEnum
    {
        return PaymentTypeEnum::DISCOUNT;
    }
}
