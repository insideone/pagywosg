<?php

namespace App\Framework\Security;

use App\Framework\Value\EnumTrait;

class RoleEnum
{
    use EnumTrait;

    const ADMIN = 'ROLE_ADMIN';
    const EVENT_MAKER = 'ROLE_EVENT_MAKER';
    const USER = 'ROLE_USER';
    const ANONYMOUS = 'ROLE_ANONYMOUS';
}
