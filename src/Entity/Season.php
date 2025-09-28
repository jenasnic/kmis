<?php

namespace App\Entity;

use App\Entity\Payment\PriceOption;
use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
class Season
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 55)]
    private string $label;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    private ?\DateTime $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    private ?\DateTime $endDate = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $paymentLink = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $licenceLink = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $pricingNote = null;

    /**
     * @var Collection<int, PriceOption>
     */
    #[ORM\OneToMany(targetEntity: PriceOption::class, mappedBy: 'season', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['rank' => 'ASC'])]
    private Collection $priceOptions;

    public function __construct(string $label)
    {
        $this->label = $label;
        $this->active = false;

        $this->priceOptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getPaymentLink(): ?string
    {
        return $this->paymentLink;
    }

    public function setPaymentLink(?string $paymentLink): self
    {
        $this->paymentLink = $paymentLink;

        return $this;
    }

    public function getPricingNote(): ?string
    {
        return $this->pricingNote;
    }

    public function setPricingNote(?string $pricingNote): self
    {
        $this->pricingNote = $pricingNote;

        return $this;
    }

    public function getLicenceLink(): ?string
    {
        return $this->licenceLink;
    }

    public function setLicenceLink(?string $licenceLink): self
    {
        $this->licenceLink = $licenceLink;

        return $this;
    }

    /**
     * @return Collection<int, PriceOption>
     */
    public function getPriceOptions(): Collection
    {
        return $this->priceOptions;
    }

    public function addPriceOption(PriceOption $priceOption): self
    {
        if (!$this->priceOptions->contains($priceOption)) {
            $this->priceOptions->add($priceOption);
        }

        return $this;
    }

    public function removePriceOption(PriceOption $priceOption): self
    {
        $this->priceOptions->removeElement($priceOption);

        return $this;
    }

    public function getDisplayLabel(): string
    {
        return sprintf('%s-%s', $this->getStartDate()?->format('Y'), $this->getendDate()?->format('Y'));
    }
}
