<?php

namespace App\Repository\Payment;

use App\Entity\Payment\DiscountCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DiscountCode>
 */
class DiscountCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscountCode::class);
    }

    public function add(DiscountCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DiscountCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<DiscountCode>
     */
    public function findOrdered(): array
    {
        /** @var array<DiscountCode> */
        return $this
            ->createQueryBuilder('discount_code')
            ->orderBy('discount_code.id', Order::Ascending->value)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param array<int> $keepIds
     */
    public function cleanCodes(array $keepIds = []): void
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->delete(DiscountCode::class, 'discount_code')
        ;

        if (!empty($keepIds)) {
            $queryBuilder
                ->where('discount_code.id NOT IN (:ids)')
                ->setParameter('ids', $keepIds)
            ;
        }

        $queryBuilder->getQuery()->execute();
    }
}
