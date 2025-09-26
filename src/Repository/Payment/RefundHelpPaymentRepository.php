<?php

namespace App\Repository\Payment;

use App\Entity\Payment\RefundHelpPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RefundHelpPayment>
 */
class RefundHelpPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefundHelpPayment::class);
    }
}
