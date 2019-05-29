<?php

namespace App\Service;

use App\Entity\Change;
use Doctrine\ORM\EntityManagerInterface;

class Changelog
{
    /** @var EntityManagerInterface */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function add(Change $change)
    {
        // is it valid change?
        if ($change->getOld() === $change->getNew()) {
            return $this;
        }
        $this->em->persist($change);
        return $this;
    }

    public function save()
    {
        $this->em->flush();
    }
}
