<?php

namespace App\Notify\Email;

use App\Framework\Notify\Email\UserEmail;

class UnlockedEmail extends UserEmail
{
    public function getPredefinedSubject(): string
    {
        return 'Event gifts have been unlocked';
    }

    public function getPredefinedTemplate()
    {
        return '@email/unlocked.html.twig';
    }
}
