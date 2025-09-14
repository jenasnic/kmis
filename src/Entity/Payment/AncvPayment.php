<?php

namespace App\Entity\Payment;

use App\Enum\PaymentTypeEnum;
use App\Repository\Payment\AncvPaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AncvPaymentRepository::class)]
#[ORM\Table(name: 'payment_ancv')]
class AncvPayment extends AbstractPayment
{
    #[ORM\Column(type: Types::STRING, length: 55)]
    #[Assert\NotBlank]
    private ?string $number = null;

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getPaymentType(): PaymentTypeEnum
    {
        return PaymentTypeEnum::ANCV;
    }
}
