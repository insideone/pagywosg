<?php

namespace App\Service;

class ClassShortNameProvider
{
    protected $names;

    public function get($class)
    {
        if (!$class) {
            return null;
        }

        if (is_object($class)) {
            $class = get_class($class);
        }

        if (isset($this->names[$class])) {
            return $this->names[$class];
        }

        return ($this->names[$class] = substr(strrchr($class, '\\'), 1));
    }
}
