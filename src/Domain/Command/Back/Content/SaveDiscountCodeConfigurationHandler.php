<?php

namespace App\Domain\Command\Back\Content;

use App\Repository\Payment\DiscountCodeRepository;
use Doctrine\ORM\EntityManagerInterface;

final class SaveDiscountCodeConfigurationHandler
{
    public function __construct(
        private readonly DiscountCodeRepository $discountCodeRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(SaveDiscountCodeConfigurationCommand $command): void
    {
        $discountCodeIds = array_filter($command->discountCodeConfiguration->getDiscountCodeIds());

        $this->discountCodeRepository->cleanCodes($discountCodeIds);

        foreach ($command->discountCodeConfiguration->discountCodes as $discountCode) {
            $this->entityManager->persist($discountCode);
        }

        $this->entityManager->flush();
    }
}
