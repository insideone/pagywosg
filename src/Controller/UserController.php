<?php

namespace App\Controller;

use App\Api\Steam\PlayerSummariesProvider;
use App\Api\Steam\SteamIdApiProvider;
use App\Entity\Event;
use App\Entity\EventEntry;
use App\Entity\Role;
use App\Entity\User;
use App\Framework\Controller\BaseController;
use App\Security\Permission\UserPermission;
use DateTime;
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
     * @param SteamIdApiProvider $idProvider
     * @return Response
     */
    public function getList(
        Request $request,
        PlayerSummariesProvider $playerSummariesProvider,
        SteamIdApiProvider $idProvider
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

    /**
     * @Route("/api/users/{user}/profile", methods={"GET"}, name="getUserProfile")
     * @param User $user
     * @return Response
     * @throws Exception
     */
    public function getUserProfile(User $user)
    {
        /** @var Event[] $participatedEvents */
        $participatedEvents = $this->em->createQueryBuilder()
            ->from(Event::class, 'event')
            ->select('event')
            ->join('event.entries', 'entry')
            ->where('entry.player = :user')
            ->setParameters([
                'user' => $user
            ])
            ->getQuery()->getResult();

        $totals = [];

        // participated events
        $totalEvents = $this->em->createQueryBuilder()
            ->from(EventEntry::class, 'entry')
            ->select('COUNT(distinct event) as cnt')
            ->where('entry.player = :user')
            ->setParameters([
                'user' => $user,
            ])
            ->join('entry.event', 'event')
            ->join('entry.player', 'player')
            ->groupBy('player')
            ->getQuery()->getResult();
        // if user never participated, the result is empty, and getSingleScalarResult() throws error.

        $totals['events'] = (int)reset($totalEvents)['cnt'];

        $totalEntries = $this->em->createQueryBuilder()
            ->from(EventEntry::class, 'entry')
            ->select('COUNT(entry) as cnt')
            ->where('entry.player = :user')
            ->setParameters([
                'user' => $user,
            ])
            ->getQuery()->getResult();

        $totals['entries'] = (int)reset($totalEntries)['cnt'];

        // verified play stats
        $totalsByEvent = $this->em->createQueryBuilder()
            ->from(EventEntry::class, 'entry')
            ->select(
                //'identity(entry.player) as player',
                'sum(CASE
                            WHEN (entry.achievementsCntInitial is not null) THEN ( entry.achievementsCnt - entry.achievementsCntInitial )
                            ELSE (entry.achievementsCnt)
                        END) as achievements',
                'sum(CASE
                        WHEN (entry.playTimeInitial is not null) THEN ( entry.playTime - entry.playTimeInitial )
                        ELSE (entry.playTime)
                    END) as playTime',
                'count(distinct entry.game) as beaten',
                'identity(entry.event) as event'
            )
            ->where('entry.player = :user')
            ->andWhere('entry.playStatusVerified = true')
            ->groupBy('entry.event')
            ->orderBy('identity(entry.event)', 'DESC')
            ->setParameters([
                'user' => $user
            ])
            ->getQuery()->getResult();

        foreach ($totalsByEvent as $key => $totalsDatum) {
            $totalsByEvent[$key] = array_map(function ($value) {
                return (int)$value;
            }, $totalsDatum);
        }

        $totals['byEvent'] = $totalsByEvent;

        return $this->response([
            'totals' => $totals,
            'user' => $user,
            'events' => $participatedEvents,
        ]);
    }
}
