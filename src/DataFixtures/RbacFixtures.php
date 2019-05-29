<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\EventEntry;
use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Framework\Security\Permission\PermissionsHolder;
use App\Framework\Security\RoleEnum;
use App\Security\Permission\EventEntryPermission;
use App\Security\Permission\EventPermission;
use App\Security\Permission\UserPermission;
use App\Security\PermissionsCollection;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RbacFixtures extends Fixture
{
    const ROLE_HIERARCHY = [
        RoleEnum::ADMIN => [RoleEnum::EVENT_MAKER],
        RoleEnum::EVENT_MAKER => [RoleEnum::USER],
        RoleEnum::USER => [RoleEnum::ANONYMOUS],
        RoleEnum::ANONYMOUS => [],
    ];

    const ROLE_PERMISSIONS = [
        RoleEnum::ADMIN => [
            // It's suported to pass whole Permission class name, but it isn't needed for admin because it has a bypass
            // see BaseController::isGranted
            // EventPermission::class,
            // EventEntryPermission::class,
        ],
        RoleEnum::EVENT_MAKER => [
            [EventPermission::CREATE_OWN, Event::class],
            [EventPermission::UPDATE_OWN, Event::class],
            [EventEntryPermission::CREATE_HOSTED, EventEntry::class],
            [EventEntryPermission::UPDATE_HOSTED, EventEntry::class],
            [EventEntryPermission::DELETE_HOSTED, EventEntry::class],
            [EventEntryPermission::UPDATE_VERIFICATION_HOSTED, EventEntry::class],
            [UserPermission::SEARCH, User::class],
        ],
        RoleEnum::USER => [
            [EventEntryPermission::CREATE_OWN, EventEntry::class],
            [EventEntryPermission::UPDATE_OWN, EventEntry::class],
            [EventEntryPermission::DELETE_OWN, EventEntry::class],
        ],
    ];

    /** @var Role[] */
    protected $roles = [];

    /** @var Permission[] */
    protected $permissions = [];

    /** @var PermissionsCollection */
    protected $permissionCollection;

    public function __construct(PermissionsCollection $permissionsCollection)
    {
        $this->permissionCollection = $permissionsCollection;
    }

    public function load(ObjectManager $em)
    {
        $this->loadRoles($em);
        $this->loadPermissions($em);
        $this->loadRolePermissions($em);
        $em->flush();
    }

    /**
     * @param ObjectManager $em
     */
    protected function loadRoles(ObjectManager $em): void
    {
        foreach (array_keys(self::ROLE_HIERARCHY) as $name) {
            $role = (new Role)->setName($name);
            $em->persist($role);
            $this->roles[$name] = $role;
        }

        foreach (self::ROLE_HIERARCHY as $name => $childs) {
            foreach ($childs as $childName) {
                $this->roles[$name]->addChild($this->roles[$childName]);
            }
        }
    }

    protected function loadPermissions(ObjectManager $em)
    {
        foreach ($this->permissionCollection->getHolders() as $holder) {
            $class = $holder::getEntity();
            foreach ($holder::getOperations() as $operation) {
                $this->addPermission(
                    (new Permission)
                        ->setClass($class)
                        ->setOperation($operation),
                    $em
                );
            }
        }
    }

    protected function loadRolePermissions(ObjectManager $em)
    {
        foreach (self::ROLE_PERMISSIONS as $role => $permissions) {
            foreach ($permissions as $permission) {
                if (is_a($permission, PermissionsHolder::class, true)) {
                    $entity = $permission::getEntity();
                    foreach ($permission::getOperations() as $operation) {
                        $this->roles[$role]->addPermission($this->getPermission($operation, $entity));
                    }
                } else {
                    $this->roles[$role]->addPermission($this->getPermission(...$permission));
                }
            }
        }
    }

    protected function getPermissionKey($operation, $class)
    {
        return "{$operation}@{$class}";
    }

    protected function addPermission(Permission $permission, ObjectManager $em)
    {
        $key = $this->getPermissionKey($permission->getOperation(), $permission->getClass());
        $this->permissions[$key] = $permission;
        $em->persist($permission);
    }

    protected function getPermission($operation, $class)
    {
        $key = $this->getPermissionKey($operation, $class);
        return $this->permissions[$key];
    }
}
