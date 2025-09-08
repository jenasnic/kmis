<?php

namespace App\DataFixtures\Content;

use App\DataFixtures\Factory\Content\SportingFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SportingFixtures extends Fixture
{
    public const SPORTING_COUNT = 4;

    public function load(ObjectManager $manager): void
    {
        for ($i = self::SPORTING_COUNT; $i > 0; --$i) {
            SportingFactory::createOne([
                'active' => true,
                'rank' => $i,
            ]);
        }
    }
}
