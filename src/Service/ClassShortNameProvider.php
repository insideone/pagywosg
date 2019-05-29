<?php

namespace App\Service;

class ClassShortNameProvider
{
    protected $names;

    public function __invoke($name)
    {
        if (isset($this->names[$name])) {
            return $this->names[$name];
        }

        return ($this->names[$name] = substr(strrchr($name, '\\'), 1));
    }
}
