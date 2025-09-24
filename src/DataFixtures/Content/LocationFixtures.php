<?php

namespace App\DataFixtures\Content;

use App\DataFixtures\Factory\AddressFactory;
use App\DataFixtures\Factory\Content\LocationFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LocationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        LocationFactory::createOne([
            'name' => 'Liancourt',
            'localization' => 'https://www.google.com/maps/place/Collège+La+Rochefoucauld/@49.3262507,2.4592501,16z/data=!3m1!4b1!4m6!3m5!1s0x47e64ac4d94d7355:0xb2ec60327646476a!8m2!3d49.3262473!4d2.464121!16s%2Fg%2F1ttdm910',
            'latitude' => '49.3262507',
            'longitude' => '2.4592501',
            'active' => true,
            'rank' => 1,
            'address' => AddressFactory::createOne([
                'street' => 'Gymnase - Collège La Rochefoucauld',
                'street2' => 'Rue du général De Gaulle',
                'zipCode' => '60140',
                'city' => 'Liancourt',
            ]),
        ]);

        LocationFactory::createOne([
            'name' => 'Précy-sur-Oise',
            'localization' => 'https://www.google.com/maps/place/34+Sente+Sorel,+60460+Pr%C3%A9cy-sur-Oise/@49.212344,2.3721153,17z/data=!3m1!4b1!4m6!3m5!1s0x47e64f23b2c84ab9:0x601a1dd1c86a36f4!8m2!3d49.2123406!4d2.3769862!16s%2Fg%2F11c4nbmb5q',
            'latitude' => '49.212344',
            'longitude' => '2.3721153',
            'active' => true,
            'rank' => 2,
            'address' => AddressFactory::createOne([
                'street' => 'Dojo - 34 sente Sorel',
                'street2' => null,
                'zipCode' => '60460',
                'city' => 'Précy-sur-Oise',
            ]),
        ]);

        LocationFactory::createOne([
            'name' => 'Villers-Sous-Saint-Leu',
            'localization' => 'https://www.google.com/maps/place/23+Rue+du+Castel,+60340+Villers-Sous-Saint-Leu/@49.2142549,2.396175,17z/data=!3m1!4b1!4m5!3m4!1s0x47e648d4a31f8c93:0x7abe632340c2a373!8m2!3d49.2142549!4d2.3983637',
            'latitude' => '49.2142549',
            'longitude' => '2.396175',
            'active' => true,
            'rank' => 3,
            'address' => AddressFactory::createOne([
                'street' => '23 rue du Castel',
                'street2' => null,
                'zipCode' => '60340',
                'city' => 'Villers-Sous-Saint-Leu',
            ]),
        ]);
    }
}
