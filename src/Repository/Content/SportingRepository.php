<?php

namespace App\Repository\Content;

use App\Entity\Content\Sporting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sporting>
 */
class SportingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sporting::class);
    }

    public function add(Sporting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sporting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<Sporting>
     */
    public function findAllOrdered(): array
    {
        /** @var array<Sporting> */
        return $this
            ->createQueryBuilder('sporting')
            ->orderBy('sporting.rank', Order::Ascending->value)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<Sporting>
     */
    public function findOrdered(): array
    {
        /** @var array<Sporting> */
        return $this
            ->createQueryBuilder('sporting')
            ->andWhere('sporting.active = TRUE')
            ->orderBy('sporting.rank', Order::Ascending->value)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getFirstRank(): int
    {
        $query = $this
            ->createQueryBuilder('sporting')
            ->select('MIN(sporting.rank)')
            ->getQuery()
        ;

        /** @var int $minRank */
        $minRank = $query->getSingleScalarResult() ?? 0;

        return $minRank;
    }
}
