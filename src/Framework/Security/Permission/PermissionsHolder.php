<?php

namespace App\Framework\Security\Permission;

use App\Entity\User;

interface PermissionsHolder
{
    public static function getEntity() : string;

    public static function getOperations() : array;

    public static function getOwner($object) : ?User;

    public static function getHost($object) : ?User;
}
