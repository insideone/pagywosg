<?php

namespace App\Controller;

use App\DBAL\Types\PlayStatusType;
use App\Entity\Event;
use App\Entity\EventEntry;
use App\Framework\Controller\BaseController;
use App\Security\Permission\EventEntryPermission;
use App\Security\Permission\EventPermission;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventEntryController extends BaseController
{
    /**
     * @param string $attribute
     * @param Event $event
     * @param EventEntry|null $eventEntry
     * @return JsonResponse
     */
    protected function processEventEntry(string $attribute, Event $event, EventEntry $eventEntry = null)
    {
        if ($event === null) {
            return $this->notFoundResponse('event');
        }

        if ($eventEntry && !$eventEntry->getPlayer()->getId()) {
            return $this->errorResponse("You can't change entry player");
        }

        if ($eventEntry && $eventEntry->getEvent() !== $event) {
            return $this->errorResponse("This isn't entry from requested event");
        }

        if (!$event->isActive()) {
            return $this->errorResponse("This event isn't active at this moment");
        }

        /** @var EventEntry $eventEntry */
        try {
            $eventEntry = $this->getValidatedEntity([
                'object_to_populate' => $eventEntry,
            ]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }

        $eventEntry->setEvent($event);

        if (!$this->isGranted($attribute, $eventEntry)) {
            return $this->forbiddenResponse("{$attribute} isn't granted");
        }

        try {
            $this->saveEntity($eventEntry);
        } catch (ForeignKeyConstraintViolationException $e) {
            return $this->exceptionResponse($e);
        } catch (UniqueConstraintViolationException $e) {
            return $this->errorResponse("It seems like you've already created such entry");
        }

        $permissions = $this->permissionTeller->isGrantedMultiple([
            'update_own' => 'update',
            'delete_own' => 'delete',
            'update_verification_hosted' => 'update_verification',
        ], $eventEntry);

        return $this->response([
            'entry' => $eventEntry,
            'permissions' => $permissions,
        ]);
    }

    /**
     * @Route("/api/events/{event}/entries", methods={"POST"}, name="createEventEntry")
     * @param Event $event
     * @return JsonResponse
     */
    public function create(Event $event = null)
    {
        if ($event === null) {
            return $this->notFoundResponse('event');
        }

        return $this->processEventEntry(EventPermission::CREATE_OWN, $event);
    }

    /**
     * @Route("/api/events/{event}/entries/{eventEntry}", methods={"PUT"}, name="updateEventEntry")
     * @param Event $event
     * @param EventEntry $eventEntry
     * @return JsonResponse
     */
    public function update(Event $event = null, EventEntry $eventEntry = null)
    {
        if ($eventEntry === null) {
            return $this->notFoundResponse('event entry');
        }

        return $this->processEventEntry(EventPermission::UPDATE_OWN, $event, $eventEntry);
    }

    /**
     * @Route("/api/events/{event}/entries/{eventEntry}", methods={"DELETE"}, name="deleteEventEntry")
     * @param Event|null $event
     * @param EventEntry|null $eventEntry
     * @return JsonResponse
     */
    public function delete(Event $event = null, EventEntry $eventEntry = null)
    {
        if (!$event) {
            return $this->notFoundResponse('event');
        }

        if (!$eventEntry) {
            return $this->notFoundResponse('event entry');
        }

        if (!$this->isGranted(EventEntryPermission::DELETE_OWN, $eventEntry)) {
            return $this->forbiddenResponse(EventEntryPermission::DELETE_OWN."isn't granted");
        }

        $this->removeEntity($eventEntry);

        return $this->response();
    }

    /**
     * @Route("/api/events/{event}/entries/{entry}/verified/{field}", methods={"PUT", "DELETE"})
     * @param Request $request
     * @param EventEntry $entry
     * @param string $field
     * @return JsonResponse
     */
    public function setVerification(Request $request, EventEntry $entry, string $field)
    {
        $isGoingToBeVerified = $request->getMethod() === Request::METHOD_PUT;

        if ($field === 'playStatusVerified' && $isGoingToBeVerified) {
            if (!$entry->isVerified()) {
                return $this->errorResponse("You must check the entry game firstly");
            } elseif ($entry->getPlayStatus() === PlayStatusType::UNFINISHED) {
                return $this->errorResponse("You can't verify unfinished status");
            }
        }

        if (!$this->isGranted(EventEntryPermission::UPDATE_VERIFICATION_HOSTED, $entry)) {
            return $this->errorResponse("You aren't allowed to verify this entry");
        }

        if (!$entry->setVerification($field, $isGoingToBeVerified)) {
            return $this->errorResponse("Such verification type isn't supported: {$field}");
        }

        try {
            $this->saveEntity($entry);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }

        return $this->response();
    }

    protected function getServedEntity()
    {
        return EventEntry::class;
    }
}
