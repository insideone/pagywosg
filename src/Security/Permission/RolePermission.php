<?php

namespace App\Security\Permission;

use App\Entity\Role;
use App\Entity\User;
use App\Framework\Security\Permission\CrudOwnHostedPermissions;

class RolePermission extends CrudOwnHostedPermissions
{
    public static function getEntity(): string
    {
        return Role::class;
    }

    public static function getOwner($role): ?User
    {
        return null;
    }

    public static function getHost($role): ?User
    {
        return null;
    }
}
