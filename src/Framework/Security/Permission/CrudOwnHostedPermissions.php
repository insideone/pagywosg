<?php

namespace App\Framework\Security\Permission;

use App\Framework\Value\EnumTrait;

abstract class CrudOwnHostedPermissions implements PermissionsHolder
{
    use EnumTrait;

    const CREATE_OWN = 'create_own';
    const CREATE_HOSTED = 'create_hosted';
    const CREATE_ANY = 'create_any';

    const READ_OWN = 'read_own';
    const READ_HOSTED = 'read_hosted';
    const READ_ANY = 'read_any';

    const UPDATE_OWN = 'update_own';
    const UPDATE_HOSTED = 'update_hosted';
    const UPDATE_ANY = 'update_any';

    const DELETE_OWN = 'delete_own';
    const DELETE_HOSTED = 'delete_hosted';
    const DELETE_ANY = 'delete_any';

    abstract static public function getEntity() : string;

    public static function getOperations() : array
    {
        return self::getEnumValues();
    }
}
