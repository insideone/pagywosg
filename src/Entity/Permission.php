<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Fxp\Component\Security\Model\PermissionInterface;
use Fxp\Component\Security\Model\Traits\PermissionTrait;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="permissions",
 *     indexes={
 *         @ORM\Index(name="idx_permission_operation", columns={"operation"}),
 *         @ORM\Index(name="idx_permission_class", columns={"class"}),
 *         @ORM\Index(name="idx_permission_field", columns={"field"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uniq_permission", columns={"operation", "class", "field"})
 *     }
 * )
 */
class Permission implements PermissionInterface
{
    use PermissionTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"export"})
     */
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Permission
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
