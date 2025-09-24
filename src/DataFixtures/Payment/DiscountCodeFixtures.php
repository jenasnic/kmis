<?php

namespace App\DataFixtures\Payment;

use App\DataFixtures\ConfigurationFixtures;
use App\Entity\Payment\DiscountCode;
use App\Enum\RefundHelpEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DiscountCodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(DiscountCode::create('PASS_CITIZEN', [RefundHelpEnum::PASS_CITIZEN]));
        $manager->persist(DiscountCode::create('PASS_SPORT', [RefundHelpEnum::PASS_SPORT]));
        $manager->persist(DiscountCode::create('BOTH', [RefundHelpEnum::PASS_CITIZEN, RefundHelpEnum::PASS_SPORT]));

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ConfigurationFixtures::class,
        ];
    }
}
