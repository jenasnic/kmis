<?php

namespace App\Domain\Command\Back\Content;

use App\Entity\Content\Sporting;

class SaveSportingCommand
{
    public function __construct(public Sporting $sporting)
    {
    }
}
