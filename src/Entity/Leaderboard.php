<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Leaderboard
 * @package App\Entity
 */
class Leaderboard
{
    /**
     * @var LeaderboardEntry[]
     * @Groups({"export"})
     */
    protected $entries = [];

    /**
     * @var Event[]
     * @Groups({"export"})
     */
    protected $events = [];

    public function __construct()
    {
        $this->events = new ArrayCollection;
    }

    public function applyEvent(Event $event)
    {
        if ($this->events->contains($event)) {
            return $this;
        }

        $this->events->add($event);

        foreach ($event->getEntries() as $entry) {
            $this->applyEntry($entry);
        }

        return $this;
    }

    public function applyEntry(EventEntry $eventEntry)
    {
        $player = $eventEntry->getPlayer();
        $playerId = $player->getId();

        if (!isset($this->entries[$playerId])) {
            $this->entries[$playerId] = new LeaderboardEntry($player);
        }

        $this->entries[$playerId]->apply($eventEntry);

        return $this;
    }

    /**
     * @return Event[]
     */
    public function getEvents(): array
    {
        return $this->events->toArray();
    }

    /**
     * @param Event[] $events
     * @return Leaderboard
     */
    public function setEvents(array $events): Leaderboard
    {
        $this->events = $events;
        return $this;
    }

    /**
     * @return LeaderboardEntry[]
     */
    public function getEntries(): array
    {
        return array_values($this->entries);
    }

    /**
     * @param LeaderboardEntry[] $entries
     * @return Leaderboard
     */
    public function setEntries(array $entries): Leaderboard
    {
        $this->entries = $entries;
        return $this;
    }
}
