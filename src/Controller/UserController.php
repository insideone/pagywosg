<?php

namespace App\Controller;

use App\Api\Steam\PlayerSummariesProvider;
use App\Api\Steam\SteamIdInnerProvider;
use App\Entity\Role;
use App\Entity\User;
use App\Framework\Controller\BaseController;
use App\Security\Permission\UserPermission;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidApiResponseException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidUserClassException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    /**
     * @Route("/api/users", methods={"GET"}, name="getUsers")
     * @param Request $request
     * @param PlayerSummariesProvider $playerSummariesProvider
     * @param SteamIdInnerProvider $idProvider
     * @return Response
     */
    public function getList(
        Request $request,
        PlayerSummariesProvider $playerSummariesProvider,
        SteamIdInnerProvider $idProvider
    ) {
        $qb = $this->em->createQueryBuilder()
            ->from(User::class, 'user')
            ->select('user');

        $query = null;
        if ($request->query->has('query') && ($query = $request->query->get('query'))) {
            if (!$this->isGranted(UserPermission::SEARCH, User::class)) {
                return $this->errorResponse("You aren't granted to search users");
            }

            // suitable url formats
            // https://steamcommunity.com/id/insideone/
            // https://steamcommunity.com/profiles/76561198106641748
            if (preg_match('~(id|profiles)/(\S+)~', $query, $m)) {
                [, $type, $query] = $m;
            }

            if (is_numeric($query)) {
                $qb
                    ->andWhere('user.steamId = :steamId')
                    ->orWhere('user.profileName = :steamId')
                    ->setParameter('steamId', $query);
            } else {
                $qb
                    ->andWhere('user.profileName like :profileName')
                    ->setParameter('profileName', "%{$query}%");
            }
        } elseif (!$this->isGranted(UserPermission::READ_ANY, User::class)) {
            return $this->errorResponse("You aren't granted to read all users");
        }

        $users = $qb->getQuery()->getResult();

        // api lookup
        if (!$users && $query) {
            if (!is_numeric($query) || (isset($type) && $type === 'id')) {
                try {
                    $query = $idProvider->fetch($query);
                } catch (Exception|GuzzleException $e) {
                    return $this->errorResponse("Can't resolve an username: {$e->getMessage()}");
                }
            }

            if ($query) {

                try {
                    $user = $playerSummariesProvider->fetch($query);
                } catch (InvalidUserClassException|InvalidApiResponseException $e) {
                    return $this->exceptionResponse($e);
                }

                if ($user) {
                    $users = [$user];
                }
            }
        }

        return $this->response(['users' => $users]);
    }

    /**
     * @Route("/api/users/{user}/roles/{role}", methods={"PUT"}, name="addUserRole")
     * @param User $user
     * @param Role $role
     * @return Response
     */
    public function addUserRole(User $user, Role $role)
    {
        if (!$this->isGranted(UserPermission::UPDATE_ANY, $user)) {
            return $this->forbiddenResponse(UserPermission::UPDATE_ANY." isn't granted");
        }

        $user->addRole($role->getName());
        try {
            $this->saveEntity($user);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }

        return $this->response();
    }

    /**
     * @Route("/api/users/{user}/roles/{role}", methods={"DELETE"}, name="deleteUserRole")
     * @param User $user
     * @param Role $role
     * @return Response
     */
    public function deleteUserRole(User $user, Role $role)
    {
        if (!$this->isGranted(UserPermission::UPDATE_ANY, $user)) {
            return $this->forbiddenResponse(UserPermission::UPDATE_ANY." isn't granted");
        }

        $user->removeRole($role->getName());
        try {
            $this->saveEntity($user);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }

        return $this->response();
    }
}
