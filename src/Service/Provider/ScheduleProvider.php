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
            $result[$schedule->getSporting()->getId()][] = $schedule;
        }

        return $result;
    }

    /**
     * @return array<array{location: Location, days: array<array{day: DayOfWeekEnum, start: string, end: string}>}>
     */
    public function forContact(): array
    {
        /** @var array<Schedule> $schedules */
        $schedules = $this->scheduleRepository->findOrderedSchedulesWithSporting();

        $result = [];
        foreach ($schedules as $schedule) {
            $calendar = $schedule->getCalendar();
            $locationKey = $calendar->getLocation()->getId();
            $dayKey = $calendar->getDay()->name;

            if (!array_key_exists($locationKey, $result)) {
                $result[$locationKey] = [
                    'location' => $calendar->getLocation(),
                    'days' => [],
                ];
            }

            if (!array_key_exists($dayKey, $result[$locationKey]['days'])) {
                $result[$locationKey]['days'][$dayKey] = [
                    'day' => $calendar->getDay(),
                    'start' => $schedule->getStart(),
                    'end' => $schedule->getEnd(),
                ];
            } else {
                $result[$locationKey]['days'][$dayKey]['end'] = $schedule->getEnd();
            }
        }

        return $result;
    }
}