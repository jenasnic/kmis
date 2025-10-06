<?php

namespace App\Repository;

use App\Entity\Registration;
use App\Enum\GenderEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Registration>
 */
class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registration::class);
    }

    public function add(Registration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Registration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return \Generator<Registration>
     */
    public function findForExport(): \Generator
    {
        $queryBuilder = $this->createQueryBuilder('registration');

        $queryBuilder
            ->innerJoin('registration.purpose', 'purpose')
            ->innerJoin('registration.adherent', 'adherent')
            ->innerJoin('registration.season', 'season')
            ->addOrderBy('season.label', Order::Descending->value)
            ->addOrderBy('adherent.lastName', Order::Ascending->value)
            ->addOrderBy('adherent.firstName', Order::Ascending->value)
        ;

        /** @var Registration $item */
        foreach ($queryBuilder->getQuery()->toIterable() as $item) {
            yield $item;
        }
    }

    public function getReceiptForSeason(int $seasonId): float
    {
        $queryBuilder = $this->createQueryBuilder('registration');

        $queryBuilder
            ->select('SUM(price_option.amount)')
            ->innerJoin('registration.season', 'season')
            ->innerJoin('registration.priceOption', 'price_option')
            ->andWhere('season.id = :seasonId')
            ->setParameter('seasonId', $seasonId)
        ;

        /** @var float */
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array<array{gender: GenderEnum, count: int}>
     */
    public function getDistributionByGender(int $seasonId): array
    {
        $queryBuilder = $this->createQueryBuilder('registration');

        $queryBuilder
            ->select('adherent.gender AS gender')
            ->addSelect('COUNT(adherent.gender) AS count')
            ->innerJoin('registration.adherent', 'adherent')
            ->innerJoin('registration.season', 'season')
            ->groupBy('adherent.gender')
            ->andWhere('season.id = :seasonId')
            ->setParameter('seasonId', $seasonId)
        ;

        /** @var array<array{gender: GenderEnum, count: int}> */
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @return array<array{id: string, priceOption: string, count: int}>
     */
    public function getDistributionByPricingOption(int $seasonId): array
    {
        $queryBuilder = $this->createQueryBuilder('registration');

        $queryBuilder
            ->select('CONCAT(\'price-\', price_option.id) AS id')
            ->addSelect('price_option.label AS priceOption')
            ->addSelect('COUNT(price_option.id) AS count')
            ->innerJoin('registration.priceOption', 'price_option')
            ->innerJoin('registration.season', 'season')
            ->groupBy('price_option.id')
            ->andWhere('season.id = :seasonId')
            ->setParameter('seasonId', $seasonId)
            ->addOrderBy('count', Order::Descending->value)
        ;

        /** @var array<array{id: string, priceOption: string, count: int}> */
        return $queryBuilder->getQuery()->getArrayResult();
    }
}
