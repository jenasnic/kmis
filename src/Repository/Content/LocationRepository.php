<?php

namespace App\Repository\Content;

use App\Entity\Content\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function add(Location $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Location $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<Location>
     */
    public function findAllOrdered(): array
    {
        /** @var array<Location> */
        return $this
            ->createQueryBuilder('location')
            ->orderBy('location.rank', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<Location>
     */
    public function findOrdered(): array
    {
        /** @var array<Location> */
        return $this
            ->createQueryBuilder('location')
            ->andWhere('location.active = TRUE')
            ->orderBy('location.rank', Criteria::ASC)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getFirstRank(): int
    {
        $query = $this
            ->createQueryBuilder('location')
            ->select('MIN(location.rank)')
            ->getQuery()
        ;

        /** @var int $minRank */
        $minRank = $query->getSingleScalarResult() ?? 0;

        return $minRank;
    }
}
