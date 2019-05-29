<?php

namespace App\Security\Permission;

use App\Entity\Event;
use App\Entity\User;
use App\Framework\Security\Permission\CrudOwnHostedPermissions;

class EventPermission extends CrudOwnHostedPermissions
{
    public static function getEntity(): string
    {
        return Event::class;
    }

    /**
     * @param Event $event
     * @return User|null
     */
    public static function getOwner($event): ?User
    {
        return $event ? $event->getHost() : null;
    }

    public static function getHost($object): ?User
    {
        return null;
    }
}
