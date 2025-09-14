<?php

namespace App\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Address
{
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $street;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $street2;

    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    #[Assert\NotBlank]
    private ?string $zipCode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $city;

    public function __construct(
        ?string $street = null,
        ?string $street2 = null,
        ?string $zipCode = null,
        ?string $city = null,
    ) {
        $this->street = $street;
        $this->street2 = $street2;
        $this->zipCode = $zipCode;
        $this->city = $city;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getStreet2(): ?string
    {
        return $this->street2;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function __toString(): string
    {
        return empty($this->street2)
            ? sprintf('%s - %s %s', $this->street, $this->zipCode, $this->city)
            : sprintf('%s - %s - %s %s', $this->street, $this->street2, $this->zipCode, $this->city)
        ;
    }
}
