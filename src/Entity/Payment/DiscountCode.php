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
     * @param array<RefundHelpEnum> $refundHelps
     */
    public function matchRefundHelps(array $refundHelps): bool
    {
        $stringifyRefundHelps = array_map(fn (RefundHelpEnum $refundHelp) => $refundHelp->value, $refundHelps);

        $diffs = array_diff($stringifyRefundHelps, $this->refundHelps);

        return 0 === count($diffs);
    }

    /**
     * @param array<RefundHelpEnum> $refundHelps
     */
    public static function create(string $code, array $refundHelps): self
    {
        $refundHelpCode = new self();
        $refundHelpCode->setCode($code);

        foreach ($refundHelps as $refundHelp) {
            $refundHelpCode->addRefundHelp($refundHelp);
        }

        return $refundHelpCode;
    }
}
