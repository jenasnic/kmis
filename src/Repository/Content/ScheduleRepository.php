<?php

namespace App\Repository\Content;

use App\Entity\Content\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Schedule>
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    /**
     * @return array<int, array<Schedule>>
     */
    public function getSchedulesIndexedBySporting(): array
    {
        $queryBuilder = $this->createQueryBuilder('schedule');

        $queryBuilder
            ->innerJoin('schedule.sporting', 'sporting', Join::WITH, 'sporting.active = TRUE')
            ->innerJoin('schedule.calendar', 'calendar')
            ->innerJoin('calendar.location', 'location', Join::WITH, 'location.active = TRUE')
            ->addOrderBy('calendar.day', 'ASC')
            ->addOrderBy('schedule.start', 'ASC')
            ->addOrderBy('location.rank', 'ASC')
        ;

        $schedules = $queryBuilder->getQuery()->getResult();

        $result = [];
        foreach ($schedules as $schedule) {
            $result[$schedule->getSporting()->getId()][] = $schedule;
        }

        return $result;
    }
}
