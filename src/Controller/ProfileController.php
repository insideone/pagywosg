<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Framework\Controller\BaseController;
use App\Security\Permission\EventPermission;
use App\Security\Permission\UserPermission;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfileController extends BaseController
{
    /**
     * @Route("/api/profile", name="getUser", methods={"GET"})
     * @param User|null $user
     * @return JsonResponse
     */
    public function read(User $user = null)
    {
        $user = $user ?: $this->getUser();

        $permissions = [];

        $permissionsToCheck = [
            [UserPermission::READ_ANY, User::class],
            [EventPermission::CREATE_OWN, Event::class],
        ];

        foreach ($permissionsToCheck as [$attribute, $entity]) {
            $keyEntity = lcfirst(str_replace('App\Entity\\', '', $entity));
            $permissions["{$attribute}:{$keyEntity}"] = $this->isGranted($attribute, $entity);
        }

        return $this->response([
            'user' => $user,
            'permissions' => $permissions,
        ]);
    }
}
