<?php

namespace App\Repository\Content;

use App\Entity\Content\Calendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Calendar>
 */
class CalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendar::class);
    }

    public function add(Calendar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Calendar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<Calendar>
     */
    public function findAllOrdered(): array
    {
        /** @var array<Calendar> */
        return $this
            ->createQueryBuilder('calendar')
            ->innerJoin('calendar.location', 'location')
            ->addOrderBy('calendar.day', Criteria::ASC)
            ->addOrderBy('location.name', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }

}
