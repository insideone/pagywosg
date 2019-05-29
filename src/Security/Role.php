<?php

namespace App\Security;

use Symfony\Component\Security\Core\Role\Role as BaseRole;

class Role extends BaseRole
{
    public function __toString()
    {
        return $this->getRole();
    }
}
