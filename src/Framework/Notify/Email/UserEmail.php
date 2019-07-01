<?php

namespace App\Framework\Notify\Email;

use App\Entity\User;
use App\Framework\Exceptions\UserCantBeNotifiedException;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;

abstract class UserEmail extends BaseEmail
{
    /**
     * UserEmail constructor.
     * @param User $user
     * @param Headers|null $headers
     * @param AbstractPart|null $body
     * @throws UserCantBeNotifiedException
     */
    public function __construct(User $user, Headers $headers = null, AbstractPart $body = null)
    {
        $to = $user->getEmail();
        if (!$to) {
            throw new UserCantBeNotifiedException($user->getSteamId());
        }

        parent::__construct($headers, $body);
        $this->to($to);
    }
}
