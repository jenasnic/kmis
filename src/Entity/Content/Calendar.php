<?php

namespace App\Entity\Content;

use App\Enum\DayOfWeekEnum;
use App\Repository\Content\CalendarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER, enumType: DayOfWeekEnum::class)]
    private DayOfWeekEnum $day;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Location $location = null;

    /**
     * @var Collection<int, Schedule>
     */
    #[ORM\OneToMany(targetEntity: Schedule::class, mappedBy: 'calendar', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['start' => 'ASC'])]
    private Collection $schedules;

    public function __construct()
    {
        $this->day = DayOfWeekEnum::MONDAY;

        $this->schedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): DayOfWeekEnum
    {
        return $this->day;
    }

    public function setDay(DayOfWeekEnum $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, Schedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules->add($schedule);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        $this->schedules->removeElement($schedule);

        return $this;
    }

    public function displaySchedules(): string
    {
        $schedulesAsString = array_map(fn (Schedule $schedule) => $schedule->displaySchedule(), $this->schedules->toArray());

        return implode(' / ', $schedulesAsString);
    }
}
