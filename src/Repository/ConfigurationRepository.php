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

    public function getOrCreate(string $key): Configuration
    {
        $configuration = $this->find($key);

        if (null === $configuration) {
            $configuration = new Configuration($key);
        }

        return $configuration;
    }
}
