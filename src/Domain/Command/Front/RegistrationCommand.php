<?php

namespace App\Domain\Command\Front;

use App\Entity\Registration;

class RegistrationCommand
{
    public function __construct(public Registration $registration)
    {
    }
}
