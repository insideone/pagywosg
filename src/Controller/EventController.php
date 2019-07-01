<?php

namespace App\Controller;

use App\DBAL\Types\PlayStatusType;
use App\Entity\Event;
use App\Entity\EventEntry;
use App\Entity\GameCategory;
use App\Entity\Leaderboard;
use App\Framework\Controller\BaseController;
use App\Framework\Exceptions\UserCantBeNotifiedException;
use App\Notify\Email\UnlockedEmail;
use App\Security\Permission\EventPermission;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends BaseController
{
    /**
     * @Route("/api/events", name="getEvents", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getList(Request $request, MailerInterface $mailer)
    {
        try {
            $mailer->send(new UnlockedEmail($this->getUser()));
        } catch (UserCantBeNotifiedException|TransportExceptionInterface $e) {
            return $this->exceptionResponse($e);
        }

        $inputFilter = $request->get('filter');

        $filter = [];

        $singleEvent = false;

        if ($inputFilter['id']) {
            $singleEvent = true;
            $filter['id'] = $inputFilter['id'];
        }

        /** @var Event[] $events */
        $events = $this->em->getRepository(Event::class)->findBy($filter, ['id' => 'asc']);
        $events = array_combine(array_map(function(Event $e) { return $e->getId(); }, $events), $events);

        $qb = $this->em->createQueryBuilder();

        $participationCounters = $p = $qb
            ->from(EventEntry::class, 'entry')
            ->join('entry.event', 'event')
            ->groupBy('entry.event')
            ->select('event.id', $qb->expr()->countDistinct('entry.player').' as cnt')
            ->where('entry.event in (:events)')
            ->setParameter('events', $events)
            ->getQuery()->getResult();

        foreach ($participationCounters as ['id' => $eventId, 'cnt' => $counter]) {
            $events[$eventId]->setParticipantCount($counter);
        }

        if (!$singleEvent) {
            $qb = $this->em->createQueryBuilder();

            $entriesCounters = $p = $qb
                ->from(EventEntry::class, 'entry')
                ->join('entry.event', 'event')
                ->groupBy('entry.event')
                ->select('event.id', $qb->expr()->countDistinct('entry.id').' as cnt')
                ->where('entry.event in (:events)')
                ->setParameter('events', $events)
                ->getQuery()->getResult();

            foreach ($entriesCounters as ['id' => $eventId, 'cnt' => $counter]) {
                $events[$eventId]->setEntriesCount($counter);
            }
        }

        $events = array_values($events);

        if ($singleEvent && !$events) {
            return $this->notFoundResponse('event');
        }

        $permissionsToCheck = [
            'event' => [
                'update_own' => 'update',
                'delete_own' => 'delete',
            ],
            'eventEntry' => [
                // figure out why EventPermission::UPDATE_OWN didn't work
                // debug skip such fields and $permissions are empty in result
                'update_own' => 'update',
                'delete_own' => 'delete',
                'update_verification_hosted' => 'update_verification',
            ],
        ];

        $permissions = [];

        $user = $this->getUser();

        if ($events) {
            // preload
            $this->em->getRepository(EventEntry::class)->findBy(['event' => $events]);

            foreach ($events as $event) {
                foreach ($permissionsToCheck['event'] as $eventAttribute => $shortName) {
                    $permissions["{$shortName}:event:#{$event->getId()}"] = $this->isGranted($eventAttribute, $event);
                }

                if ($user) {
                    $voteEntry = (new EventEntry)
                        ->setEvent($event)
                        ->setPlayer($user)
                    ;

                    foreach ([EventPermission::CREATE_OWN, EventPermission::CREATE_HOSTED] as $createAttribute) {
                        $permissions["{$createAttribute}:eventEntry"] = $this->isGranted(
                            $createAttribute, $voteEntry
                        );
                    }
                }

                foreach ($event->getEntries() as $entry) {
                    foreach ($permissionsToCheck['eventEntry'] as $eventEntryAttribute => $shortName) {
                        $permissions["{$shortName}:eventEntry:#{$entry->getId()}"] = $this->isGranted($eventEntryAttribute, $entry);
                    }
                }
            }
        }

        if ($singleEvent && $events) {
            $event = $events[0];
            $isUnlocked = false;

            if ($event->getHost() !== $user) {
                foreach ($event->getEntries() as $entry) {
                    if ($entry->getPlayer() !== $user || !$entry->isPlayStatusVerified()) {
                        continue;
                    }

                    $isUnlocked = true;
                    break;
                }

                if (!$isUnlocked) {
                    // to ensure to not save this change
                    $this->em->detach($event);
                    $event->setUnlocks(null);
                }
            }
        }

        return $this->response(
            [
                'event'.($singleEvent ? '' : 's') => $singleEvent ? ($events[0]) : $events,
                'permissions' => $permissions,
            ],
            [
                'callbacks' => [
                    'playStatus' => function ($value) {
                        return [
                            'id' => $value,
                            'name' => PlayStatusType::getReadableValue($value),
                        ];
                    },
                    'event' => function ($value) {
                        return $value;
                    }
                ],
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $attribute
     * @param Event|null $event
     * @return Response
     */
    protected function processEvent(Request $request, string $attribute, Event $event = null)
    {
        /** @var Event $event */
        try {
            $event = $this->getValidatedEntity([
                'object_to_populate' => $event
            ]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }

        if (!$event->getHost()) {
            $event->setHost($this->getUser());
        }

        if (!$this->isGranted($attribute, $event)) {
            return $this->forbiddenResponse("{$attribute} isn't granted");
        }

        if ($event->getStartedAt() > $event->getEndedAt()) {
            return $this->errorResponse('Beginning date must be before ending date');
        }

        $rawCategories = array_filter($request->get('gameCategories'));

        $eventCategories = $this->em
            ->getRepository(GameCategory::class)
            ->findBy(['name' => $rawCategories]);

        $newCategories = array_diff(
            $rawCategories,
            array_map(
                function (GameCategory $gameCategory) {
                    return $gameCategory->getName();
                },
                $eventCategories
            )
        );

        foreach ($newCategories as $newCategory) {
            $eventCategories[] = (new GameCategory)->setName($newCategory);
        }

        $event->setGameCategories($eventCategories);

        try {
            $this->saveEntity($event);
        } catch (ForeignKeyConstraintViolationException|UniqueConstraintViolationException $e) {
            return $this->exceptionResponse($e);
        }

        return $this->response([
            'id' => $event->getId(),
        ]);
    }

    /**
     * @Route("/api/events", name="createEvent", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        return $this->processEvent($request, EventPermission::CREATE_OWN);
    }

    /**
     * @Route("/api/events/{event}", methods={"PUT"}, name="updateEvent")
     * @param Request $request
     * @param Event|null $event
     * @return Response
     */
    public function update(Request $request, Event $event = null)
    {
        if ($event === null) {
            return $this->notFoundResponse('event');
        }

        return $this->processEvent($request, EventPermission::UPDATE_OWN, $event);
    }

    /**
     * @Route("/api/events/{event}", methods={"DELETE"})
     * @param Event|null $event
     * @return JsonResponse
     */
    public function delete(Event $event = null)
    {
        if ($event === null) {
            return $this->notFoundResponse('event');
        }

        if (!$this->isGranted(EventPermission::DELETE_OWN, $event)) {
            return $this->forbiddenResponse("you aren't allowed to delete this event");
        }

        try {
            $this->removeEntity($event);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }

        return $this->response();
    }

    /**
     * @Route("/api/events/{eventId}", name="getEvent", methods={"GET"})
     * @param int $eventId
     * @return JsonResponse
     */
    public function read(int $eventId = null)
    {
        return $this->getList(new Request(['filter' => ['id' => $eventId]]));
    }

    protected function getServedEntity()
    {
        return Event::class;
    }

    /**
     * @Route("/api/events/{event}/leaderboard", methods={"GET"})
     * @param Event $event
     * @return JsonResponse
     */
    public function getLeaderboard(Event $event)
    {
        $leaderboard = new Leaderboard;
        $leaderboard->applyEvent($event);

        return $this->response(['leaderboard' => $leaderboard]);
    }
}
