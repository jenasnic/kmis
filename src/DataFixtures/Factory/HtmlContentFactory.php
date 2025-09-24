<?php

namespace App\DataFixtures\Factory;

use Faker\Factory;

final class HtmlContentFactory
{
    final public static function create(int $paragraphCount, int $charsCount): string
    {
        $faker = Factory::create('fr_FR');

        $result = '';
        for ($i = 0; $i < $paragraphCount; ++$i) {
            $result .= '<p>'.$faker->text($charsCount).'</p>';
        }

        return $result;
    }
}
