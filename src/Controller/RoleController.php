<?php

namespace App\Controller;

use App\Entity\Role;
use App\Framework\Controller\BaseController;
use App\Security\Permission\RolePermission;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends BaseController
{
    /**
     * @Route("/api/roles", methods={"GET"}, name="getRoles")
     * @return Response
     */
    public function getList()
    {
        if (!$this->isGranted(RolePermission::READ_ANY)) {
            return $this->forbiddenResponse();
        }
        return $this->response(['roles' => $this->em->getRepository(Role::class)->findBy([], ['id' => Criteria::ASC])]);
    }
}
