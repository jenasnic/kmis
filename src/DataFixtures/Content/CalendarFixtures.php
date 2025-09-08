<?php

namespace App\DataFixtures\Content;

use App\DataFixtures\Factory\Content\CalendarFactory;
use App\DataFixtures\Factory\Content\LocationFactory;
use App\Entity\Content\Location;
use App\Enum\DayOfWeekEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Zenstruck\Foundry\Persistence\Proxy;

class CalendarFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        /** @var Proxy<Location> $location */
        foreach (LocationFactory::all() as $location) {
            $days = $this->faker->randomElements(DayOfWeekEnum::cases(), 2);

            foreach ($days as $day) {
                CalendarFactory::createOne([
                    'day' => $day,
                    'location' => $location,
                ]);
            }
        }
    }

    /**
     * @return array<string>
     */
    public function getDependencies(): array
    {
        return [
            LocationFixtures::class,
            SportingFixtures::class,
        ];
    }
}
