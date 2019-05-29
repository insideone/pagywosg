<?php

namespace App\Security;

use App\Framework\Security\Permission\PermissionsHolder;

class PermissionsCollection
{
    /** @var PermissionsHolder[] */
    protected $collection = [];

    public function addHolder(PermissionsHolder $holder)
    {
        $this->collection[] = $holder;
        return $this;
    }

    /**
     * @return PermissionsHolder[]
     */
    public function getHolders()
    {
        return $this->collection;
    }

    /**
     * @param $subject
     * @return PermissionsHolder|null
     */
    public function findFor($subject)
    {
        foreach ($this->collection as $holder) {
            if (is_a($subject, $holder::getEntity(), true)) {
                return $holder;
            }
        }

        return null;
    }
}
