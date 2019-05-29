<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class GameCategoryRepository extends EntityRepository
{
    public function findByName(string $name)
    {
        return $this->findOneBy(['name' => $name]);
    }
}
