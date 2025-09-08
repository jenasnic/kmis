<?php

namespace App\DataFixtures\Content;

use App\DataFixtures\Factory\Content\CalendarFactory;
use App\DataFixtures\Factory\Content\LocationFactory;
use App\DataFixtures\Factory\Content\ScheduleFactory;
use App\DataFixtures\Factory\Content\SportingFactory;
use App\DataFixtures\Factory\SeasonFactory;
use App\DataFixtures\Payment\PriceOptionFixtures;
use App\DataFixtures\PurposeFixtures;
use App\DataFixtures\SeasonFixtures;
use App\Entity\Content\Calendar;
use App\Entity\Content\Location;
use App\Entity\Season;
use App\Enum\DayOfWeekEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Zenstruck\Foundry\Persistence\Proxy;

class ScheduleFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $times = [
            ['17h00', '19h00'],
            ['17h30', '19h30'],
            ['18h00', '20h00'],
            ['18h30', '20h30'],
            ['19h00', '21h00'],
            ['19h30', '21h30'],
        ];

        /** @var Proxy<Calendar> $calendar */
        foreach (CalendarFactory::all() as $calendar) {
            $time = $this->faker->randomElement($times);

            ScheduleFactory::createOne([
                'start' => $time[0],
                'end' => $time[1],
                'sporting' => SportingFactory::random(),
                'calendar' => $calendar,
            ]);
        }
    }

    /**
     * @return array<string>
     */
    public function getDependencies(): array
    {
        return [
            CalendarFixtures::class,
        ];
    }
}
