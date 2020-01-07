<?php

namespace App\Entity;

use App\DBAL\Types\PlayStatusType;
use App\Framework\Entity\IdentityProvider;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="event_entries",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"game_id", "player_id", "event_id"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class EventEntry implements IdentityProvider
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"export"})
     */
    private $id;

    /**
     * @var Game
     * @ORM\ManyToOne(targetEntity="Game")
     * @ORM\JoinColumn(name="game_id", nullable=false)
     * @Groups({"export", "import-createEventEntry"})
     */
    private $game;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="player_id", nullable=false)
     * @Groups({"export", "import-createEventEntry"})
     */
    private $player;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="entries", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"export"})
     */
    private $event;

    /**
     * @var GameCategory
     * @ORM\ManyToOne(targetEntity="GameCategory")
     * @ORM\JoinColumn(name="game_category_id", nullable=false)
     * @Groups({"export", "import-createEventEntry", "import-updateEventEntry"})
     */
    private $category;

    /**
     * @var bool The entry was verified by event holder
     * @ORM\Column(type="boolean")
     * @Groups({"export"})
     */
    private $verified = false;

    /**
     * @var string
     * @ORM\Column(type="PlayStatusType", nullable=false)
     * @Groups({"export", "import-createEventEntry", "import-updateEventEntry"})
     */
    private $playStatus = PlayStatusType::UNFINISHED;

    /**
     * @var bool Is the play status verified by event holder?
     * @ORM\Column(type="boolean")
     * @Groups({"export", "import-updateEventEntry"})
     */
    private $playStatusVerified = false;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"export", "import-updateEventEntry"})
     */
    private $playTime;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"export", "import-updateEventEntry"})
     */
    private $playTimeInitial;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"export", "import-updateEventEntry"})
     */
    private $achievementsCnt;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"export", "import-updateEventEntry"})
     */
    private $achievementsCntInitial;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"export", "import-createEventEntry", "import-updateEventEntry"})
     */
    private $notes;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $refreshedAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return EventEntry
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * @param Game $game
     * @return EventEntry
     */
    public function setGame(Game $game): EventEntry
    {
        $this->game = $game;
        return $this;
    }

    /**
     * @return GameCategory
     */
    public function getCategory(): GameCategory
    {
        return $this->category;
    }

    /**
     * @param GameCategory $category
     * @return EventEntry
     */
    public function setCategory(GameCategory $category): EventEntry
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

    /**
     * @param bool $verified
     * @return EventEntry
     */
    public function setVerified(bool $verified): EventEntry
    {
        $this->verified = $verified;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlayStatus(): string
    {
        return $this->playStatus;
    }

    /**
     * @param string $playStatus
     * @return EventEntry
     */
    public function setPlayStatus(string $playStatus): EventEntry
    {
        // Unfinished status turn off verification
        if ($playStatus === PlayStatusType::UNFINISHED) {
            $this->setPlayStatusVerified(false);
        }

        $this->playStatus = $playStatus;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPlayStatusVerified(): bool
    {
        return $this->playStatusVerified;
    }

    /**
     * @param bool $playStatusVerified
     * @return EventEntry
     */
    public function setPlayStatusVerified(bool $playStatusVerified): EventEntry
    {
        $this->playStatusVerified = $playStatusVerified;
        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * @param Event $event
     * @return EventEntry
     */
    public function setEvent(Event $event): EventEntry
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlayTime(): ?int
    {
        return $this->playTime;
    }

    /**
     * @param int $playTime
     * @return EventEntry
     */
    public function setPlayTime(?int $playTime): EventEntry
    {
        $this->playTime = $playTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlayTimeInitial(): ?int
    {
        return $this->playTimeInitial;
    }

    /**
     * @param int $playTimeInitial
     * @return EventEntry
     */
    public function setPlayTimeInitial(?int $playTimeInitial): EventEntry
    {
        $this->playTimeInitial = $playTimeInitial;
        return $this;
    }

    /**
     * @return int
     */
    public function getAchievementsCnt(): ?int
    {
        return $this->achievementsCnt;
    }

    /**
     * @param int $achievementsCnt
     * @return EventEntry
     */
    public function setAchievementsCnt(?int $achievementsCnt): EventEntry
    {
        $this->achievementsCnt = $achievementsCnt;
        return $this;
    }

    /**
     * @return int
     */
    public function getAchievementsCntInitial(): ?int
    {
        return $this->achievementsCntInitial;
    }

    /**
     * @param int $achievementsCntInitial
     * @return EventEntry
     */
    public function setAchievementsCntInitial(?int $achievementsCntInitial): EventEntry
    {
        $this->achievementsCntInitial = $achievementsCntInitial;
        return $this;
    }

    /**
     * @return User
     */
    public function getPlayer(): ?User
    {
        return $this->player;
    }

    /**
     * @param User $player
     * @return EventEntry
     */
    public function setPlayer(User $player): EventEntry
    {
        $this->player = $player;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return EventEntry
     */
    public function setNotes(string $notes): EventEntry
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @param string $field
     * @param bool $value
     * @return bool Is a verification setup complete?
     */
    public function setVerification(string $field, bool $value)
    {
        switch ($field) {
            case 'verified':
                $this->setVerified($value);
                break;
            case 'playStatusVerified':
                $this->setPlayStatusVerified($value);
                break;
            default:
                return false;
        }

        return true;
    }

    public function isBeaten($strict = true)
    {
        if ($strict) {
            return $this->getPlayStatus() === PlayStatusType::BEATEN;
        }

        return $this->getPlayStatus() !== PlayStatusType::UNFINISHED;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return EventEntry
     */
    public function setCreatedAt(DateTime $createdAt): EventEntry
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return EventEntry
     */
    public function setUpdatedAt(DateTime $updatedAt): EventEntry
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRefreshedAt(): ?DateTime
    {
        return $this->refreshedAt;
    }

    /**
     * @param DateTime $refreshedAt
     * @return EventEntry
     */
    public function setRefreshedAt(DateTime $refreshedAt): EventEntry
    {
        $this->refreshedAt = $refreshedAt;
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps(): void
    {
        $this->setUpdatedAt(new DateTime);
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new DateTime);
        }
    }
}
