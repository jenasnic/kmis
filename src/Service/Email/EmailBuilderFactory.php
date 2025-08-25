<?php

namespace App\Service\Email;

use Twig\Environment;

class EmailBuilderFactory
{
    public function __construct(
        private readonly Environment $twig,
        private readonly string $mailerSender,
        private readonly string $mailerContact,
    ) {
    }

    public function createEmailBuilder(): EmailBuilder
    {
        return new EmailBuilder($this->twig, $this->mailerSender, $this->mailerContact);
    }
}
