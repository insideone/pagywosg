<?php

namespace App\Entity;

use App\Framework\Entity\CommonEntityTrait;
use App\Framework\Entity\IdentityProvider;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="events")
 * @ORM\HasLifecycleCallbacks
 */
class Event implements IdentityProvider
{
    use CommonEntityTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"export"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(length=128, nullable=false)
     * @Groups({"import-createEvent", "import-updateEvent", "export"})
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"import-createEvent", "import-updateEvent", "export"})
     */
    private $description;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"export-getEvent", "import-createEvent", "import-updateEvent"})
     */
    private $unlocks;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="host_id", nullable=false)
     * @Groups({"export"})
     */
    private $host;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"import-createEvent", "import-updateEvent", "export"})
     */
    private $startedAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"import-createEvent", "import-updateEvent", "export"})
     */
    private $endedAt;

    /**
     * @var GameCategory[]
     * @ORM\ManyToMany(targetEntity="GameCategory", cascade={"persist"})
     * @ORM\JoinTable(name="events_game_categories",
     *      joinColumns=        {@ORM\JoinColumn(name="event_id")},
     *      inverseJoinColumns= {@ORM\JoinColumn(name="game_category_id")}
     * )
     * @Groups({"export"})
     */
    private $gameCategories;

    /**
     * @var EventEntry[]
     * @ORM\OneToMany(targetEntity="EventEntry", mappedBy="event", cascade={"persist"})
     * @ORM\JoinTable(name="events_entries",
     *     joinColumns=         {@ORM\JoinColumn(name="event_id")},
     *     inverseJoinColumns=  {@ORM\JoinColumn(name="event_entry_id")}
     * )
     * @Groups({"export-getEvent"})
     */
    private $entries;

    /**
     * @var int
     * @Groups({"export"})
     */
    private $participantCount;

    /**
     * @var int
     * @Groups({"export-getEvents"})
     */
    private $entriesCount;

    public function __construct()
    {
        $this->gameCategories = new ArrayCollection;
        $this->entries = new ArrayCollection;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Event
     */
    public function setName(string $name): Event
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Event
     */
    public function setDescription(string $description): Event
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return User
     */
    public function getHost(): ?User
    {
        return $this->host;
    }

    /**
     * @param User $host
     * @return Event
     */
    public function setHost(User $host): Event
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartedAt(): ?DateTime
    {
        return $this->startedAt;
    }

    /**
     * @param DateTime|string $startedAt
     * @return Event
     */
    public function setStartedAt($startedAt): Event
    {
        $this->startedAt = $this->getNormalizedDateTime($startedAt);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndedAt(): ?DateTime
    {
        return $this->endedAt;
    }

    /**
     * @param DateTime|string $endedAt
     * @return Event
     */
    public function setEndedAt($endedAt): Event
    {
        $this->endedAt = $this->getNormalizedDateTime($endedAt);
        return $this;
    }

    /**
     * @return GameCategory[]|ArrayCollection
     */
    public function getGameCategories()
    {
        return $this->gameCategories;
    }

    public function addGameCategory(GameCategory $gameCategory)
    {
        if ($this->gameCategories->contains($gameCategory)) {
            return $this;
        }

        $this->gameCategories[] = $gameCategory;
        return $this;
    }

    public function setGameCategories(iterable $gameCategories)
    {
        $this->gameCategories->clear();
        foreach ($gameCategories as $gameCategory) {
            $this->addGameCategory($gameCategory);
        }

        return $this;
    }

    /**
     * @return EventEntry[]|ArrayCollection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    public function addEntry(EventEntry $entry)
    {
        if ($this->entries->contains($entry)) {
            return $this;
        }

        $entry->setEvent($this);
        $this->entries[] = $entry;
        return $this;
    }

    /**
     * @param EventEntry[] $entries
     * @return Event
     */
    public function setEntries(array $entries): Event
    {
        $this->entries->clear();
        foreach ($entries as $entry) {
            $this->addEntry($entry);
        }

        return $this;
    }

    public function isActive()
    {
        $now = new DateTime;
        return ($this->startedAt && $now > $this->startedAt) && ($this->endedAt && $now < $this->endedAt);
    }

    /**
     * @return int
     */
    public function getParticipantCount(): ?int
    {
        return $this->participantCount;
    }

    /**
     * @param int $participantCount
     * @return Event
     */
    public function setParticipantCount(int $participantCount): Event
    {
        $this->participantCount = $participantCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getEntriesCount(): ?int
    {
        return $this->entriesCount;
    }

    /**
     * @param int $entriesCount
     * @return Event
     */
    public function setEntriesCount(int $entriesCount): Event
    {
        $this->entriesCount = $entriesCount;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnlocks(): ?string
    {
        return $this->unlocks;
    }

    /**
     * @param string $unlocks
     * @return Event
     */
    public function setUnlocks(?string $unlocks): self
    {
        $this->unlocks = $unlocks;
        return $this;
    }
}
