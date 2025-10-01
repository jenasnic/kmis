<?php

namespace App\Repository\Payment;

use App\Entity\Payment\AbstractPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AbstractPayment>
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractPayment::class);
    }

    public function add(AbstractPayment $payment, bool $flush = false): void
    {
        $this->getEntityManager()->persist($payment);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AbstractPayment $payment, bool $flush = false): void
    {
        $this->getEntityManager()->remove($payment);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<AbstractPayment>
     */
    public function findForAdherent(int $adherentId): array
    {
        /** @var array<AbstractPayment> */
        return $this
            ->createQueryBuilder('payment')
            ->innerJoin('payment.adherent', 'adherent')
            ->andWhere('adherent.id = :adherentId')
            ->setParameter('adherentId', $adherentId)
            ->addOrderBy('payment.date', Order::Descending->value)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<AbstractPayment>
     */
    public function findForSeason(int $seasonId): array
    {
        /** @var array<AbstractPayment> */
        return $this
            ->createQueryBuilder('payment')
            ->innerJoin('payment.season', 'season')
            ->andWhere('season.id = :seasonId')
            ->setParameter('seasonId', $seasonId)
            ->addOrderBy('payment.date', Order::Descending->value)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return \Generator<AbstractPayment>
     */
    public function findForExport(int $seasonId): \Generator
    {
        $result = $this->findForSeason($seasonId);

        foreach ($result as $item) {
            yield $item;
        }
    }

    public function getReceiptForSeason(int $seasonId): float
    {
        $queryBuilder = $this->createQueryBuilder('payment');

        $queryBuilder
            ->select('SUM(payment.amount)')
            ->innerJoin('payment.season', 'season')
            ->andWhere('season.id = :seasonId')
            ->setParameter('seasonId', $seasonId)
        ;

        /** @var float */
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array<array{type: string, count: int, amount: float}>
     */
    public function getDistributionByType(int $seasonId): array
    {
        $query = <<<SQL
                SELECT _payment.type AS _type, COUNT(_payment.type) AS _count, SUM(_payment.amount) AS _amount
                FROM payment AS _payment
                WHERE _payment.season_id = :seasonId
                GROUP BY _payment.type
                ORDER BY _count DESC
            SQL;

        $resultSetMapping = new ResultSetMapping();
        $resultSetMapping
            ->addScalarResult('_type', 'type')
            ->addScalarResult('_count', 'count', 'integer')
            ->addScalarResult('_amount', 'amount', 'float')
        ;

        $query = $this->getEntityManager()->createNativeQuery($query, $resultSetMapping);
        $query->setParameter('seasonId', $seasonId);

        /** @var array<array{type: string, count: int, amount: float}> */
        return $query->getArrayResult();
    }
}
