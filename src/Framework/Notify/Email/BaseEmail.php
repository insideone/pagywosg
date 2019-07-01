<?php

namespace App\Framework\Notify\Email;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

abstract class BaseEmail extends TemplatedEmail
{
    abstract public function getPredefinedSubject(): string;

    public function getPredefinedTemplate()
    {
        return '@email/default.html.twig';
    }
}
