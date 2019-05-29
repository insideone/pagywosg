<?php

namespace App\EventListener;

use App\Framework\Security\RoleEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use App\Entity\User;

class AuthenticationListener
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function process(AuthenticationEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof User) {
            return;
        }

        if ($user->hasRole(RoleEnum::USER)) {
            return;
        }

        $user->addRole(RoleEnum::USER);
        $this->em->flush();
    }
}
