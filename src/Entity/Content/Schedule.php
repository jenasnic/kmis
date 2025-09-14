<?php

namespace App\Entity\Content;

use App\Repository\Content\ScheduleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 15)]
    private ?string $start = null;

    #[ORM\Column(type: Types::STRING, length: 15)]
    private ?string $end = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $detail = null;

    #[ORM\ManyToOne(targetEntity: Sporting::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Sporting $sporting = null;

    #[ORM\ManyToOne(targetEntity: Calendar::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Calendar $calendar;

    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?string
    {
        return $this->start;
    }

    public function setStart(?string $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?string
    {
        return $this->end;
    }

    public function setEnd(?string $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getSporting(): ?Sporting
    {
        return $this->sporting;
    }

    public function setSporting(?Sporting $sporting): self
    {
        $this->sporting = $sporting;

        return $this;
    }

    public function getCalendar(): Calendar
    {
        return $this->calendar;
    }

    public function setCalendar(Calendar $calendar): self
    {
        $this->calendar = $calendar;

        return $this;
    }

    public function displaySchedule(bool $withDetail = false): string
    {
        $info = null;
        if ($withDetail) {
            $info = (null !== $this->sporting) ? $this->sporting->getName() : $this->detail;
        }

        return ($withDetail && null !== $info)
            ? sprintf('%s - %s : %s', $this->start, $this->end, $info)
            : sprintf('%s - %s', $this->start, $this->end)
        ;
    }
}
