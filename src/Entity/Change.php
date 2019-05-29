<?php

namespace App\Entity;

use App\Framework\Entity\ChangeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="changelog")
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class Change
{
    use ChangeTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
