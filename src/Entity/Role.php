<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fxp\Component\Security\Model\RoleHierarchicalInterface;
use Fxp\Component\Security\Model\Traits\RoleHierarchicalTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Fxp\Component\Security\Model\Traits\RoleTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="roles")
 */
class Role implements RoleHierarchicalInterface
{
    use RoleTrait;
    use RoleHierarchicalTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"export"})
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"export"})
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Role", mappedBy="children")
     */
    protected $parents;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="parents", cascade={"persist"})
     * @ORM\JoinTable(name="roles_children",
     *     joinColumns={@ORM\JoinColumn(name="role_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="children_role_id")}
     * )
     */
    protected $children;

    /**
     * @ORM\ManyToMany(targetEntity="Permission", inversedBy="roles", cascade={"persist"})
     * @ORM\JoinTable(name="roles_permissions",
     *     joinColumns={@ORM\JoinColumn(name="role_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="permission_id")}
     * )
     */
    protected $permissions;

    public function getId()
    {
        return $this->id;
    }
}
