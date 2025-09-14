<?php

namespace App\DataFixtures\Content;

use App\DataFixtures\Factory\Content\LocationFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LocationFixtures extends Fixture
{
    public const LOCATION_COUNT = 3;

    public function load(ObjectManager $manager): void
    {
        for ($i = self::LOCATION_COUNT; $i > 0; --$i) {
            LocationFactory::createOne([
                'active' => true,
                'rank' => $i,
            ]);
        }
    }
}
