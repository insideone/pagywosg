<?php

namespace App\Security\Permission;

use App\Entity\EventEntry;
use App\Entity\User;
use App\Framework\Security\Permission\CrudOwnHostedPermissions;

class EventEntryPermission extends CrudOwnHostedPermissions
{
    const UPDATE_VERIFICATION_OWN = 'update_verification_own';
    const UPDATE_VERIFICATION_HOSTED = 'update_verification_hosted';
    const UPDATE_VERIFICATION_ANY = 'update_verification_any';

    public static function getEntity(): string
    {
        return EventEntry::class;
    }

    /**
     * @param EventEntry $eventEntry
     * @return User|null
     */
    public static function getOwner($eventEntry): ?User
    {
        return $eventEntry ? $eventEntry->getPlayer() : null;
    }

    /**
     * @param EventEntry $eventEntry
     * @return User|null
     */
    public static function getHost($eventEntry): ?User
    {
        if (!$eventEntry) {
            return null;
        }

        $event = $eventEntry->getEvent();
        if (!$event) {
            return null;
        }

        return $event->getHost();
    }
}
