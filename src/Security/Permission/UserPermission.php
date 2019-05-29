<?php

namespace App\Security\Permission;

use App\Entity\User;
use App\Framework\Security\Permission\CrudOwnHostedPermissions;

class UserPermission extends CrudOwnHostedPermissions
{
    const SEARCH = 'search';

    public static function getEntity(): string
    {
        return User::class;
    }

    public static function getOwner($user): ?User
    {
        return $user;
    }

    public static function getHost($user): ?User
    {
        return null;
    }
}
