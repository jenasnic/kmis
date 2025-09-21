<?php

namespace App\Repository;

use App\Entity\Configuration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Configuration>
 */
class ConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configuration::class);
    }

    public function getOrCreate(string $code): Configuration
    {
        $configuration = $this->find($code);

        if (null === $configuration) {
            $configuration = new Configuration($code);
        }

        return $configuration;
    }

    /**
     * @param array<string> $codes
     *
     * @return array<Configuration>
     */
    public function findIndexedByCode(array $codes): array
    {
        $queryBuilder = $this
            ->createQueryBuilder('configuration', 'configuration.code')
            ->andWhere('configuration.code IN (:codes)')
            ->setParameter('codes', $codes)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
