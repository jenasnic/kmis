<?php

namespace App\DataFixtures\Content;

use App\DataFixtures\Factory\Content\SportingFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SportingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        SportingFactory::createOne([
            'name' => 'Krav Maga Adultes',
            'active' => true,
            'rank' => 1,
        ]);
        SportingFactory::createOne([
            'name' => 'KMix-MMA',
            'active' => true,
            'rank' => 2,
        ]);
        SportingFactory::createOne([
            'name' => 'Krav Maga Ados',
            'active' => true,
            'rank' => 3,
        ]);
        SportingFactory::createOne([
            'name' => 'Cours Élite',
            'active' => true,
            'rank' => 4,
        ]);
        SportingFactory::createOne([
            'name' => 'Cardio Fit',
            'active' => true,
            'rank' => 5,
        ]);
    }
}
