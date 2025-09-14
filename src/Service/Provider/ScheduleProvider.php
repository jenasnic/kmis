<?php

namespace App\Service\Provider;

use App\Entity\Content\Location;
use App\Entity\Content\Schedule;
use App\Enum\DayOfWeekEnum;
use App\Repository\Content\ScheduleRepository;

class ScheduleProvider
{
    public function __construct(
        private readonly ScheduleRepository $scheduleRepository,
    ) {
    }

    /**
     * @return array<int, array<Schedule>>
     */
    public function forSporting(): array
    {
        /** @var array<Schedule> $schedules */
        $schedules = $this->scheduleRepository->findOrderedSchedulesWithSporting();

        $result = [];
        foreach ($schedules as $schedule) {
            /** @var int $sportingId */
            $sportingId = $schedule->getSporting()?->getId();
            $result[$sportingId][] = $schedule;
        }

        return $result;
    }

    /**
     * @return array<int, array{location: Location, days: array<string, array{day: DayOfWeekEnum, start: string, end: string}>}>
     */
    public function forContact(): array
    {
        /** @var array<Schedule> $schedules */
        $schedules = $this->scheduleRepository->findOrderedSchedulesWithSporting();

        $result = [];
        foreach ($schedules as $schedule) {
            $calendar = $schedule->getCalendar();
            $dayKey = $calendar->getDay()->name;

            /** @var Location $location */
            $location = $calendar->getLocation();
            /** @var int $locationId */
            $locationId = $location->getId();

            if (!array_key_exists($locationId, $result)) {
                $result[$locationId] = [
                    'location' => $location,
                    'days' => [],
                ];
            }

            /** @var string $start */
            $start = $schedule->getStart();
            /** @var string $end */
            $end = $schedule->getEnd();

            if (!array_key_exists($dayKey, $result[$locationId]['days'])) {
                $result[$locationId]['days'][$dayKey] = [
                    'day' => $calendar->getDay(),
                    'start' => $start,
                    'end' => $end,
                ];
            } else {
                $result[$locationId]['days'][$dayKey]['end'] = $end;
            }
        }

        return $result;
    }
}
