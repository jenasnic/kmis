<?php

namespace App\Entity\Payment;

use App\Enum\RefundHelpEnum;
use App\Repository\Payment\DiscountCodeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountCodeRepository::class)]
class DiscountCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 25)]
    private string $code;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: Types::JSON)]
    protected array $refundHelps = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return array<RefundHelpEnum>
     */
    public function getRefundHelps(): array
    {
        return array_map(fn (string $value) => RefundHelpEnum::from($value), $this->refundHelps);
    }

    public function addRefundHelp(RefundHelpEnum $refundHelp): self
    {
        $this->refundHelps[] = $refundHelp->value;

        return $this;
    }

    public function removeRefundHelp(RefundHelpEnum $refundHelp): self
    {
        $this->refundHelps = array_filter($this->refundHelps, fn ($current) => $current !== $refundHelp->value);

        return $this;
    }

    /**
     * @param array<RefundHelpEnum> $refundHelpEnums
     */
    public static function create(string $code, array $refundHelpEnums): self
    {
        $refundHelpCode = new self();
        $refundHelpCode->setCode($code);

        foreach ($refundHelpEnums as $refundHelpEnum) {
            $refundHelpCode->addRefundHelp($refundHelpEnum);
        }

        return $refundHelpCode;
    }
}
