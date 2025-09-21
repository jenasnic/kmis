<?php

namespace App\Domain\Model\Content;

use App\Entity\Configuration;

class TextConfiguration
{
    public Configuration $homePresentation;

    public Configuration $contact;

    public function __construct(Configuration $homePresentation, Configuration $contact)
    {
        $this->homePresentation = $homePresentation;
        $this->contact = $contact;
    }
}
