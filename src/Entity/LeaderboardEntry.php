<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class LeaderboardEntry
{
    /**
     * @var User
     * @Groups("export")
     */
    protected $player;

    /**
     * @var int
     * @Groups("export")
     */
    protected $achievements;

    /**
     * @var int
     */
    protected $minutes;

    /**
     * @var int
     * @Groups("export")
     */
    protected $beaten;

    public function __construct(User $player)
    {
        $this->player = $player;
    }

    public function apply(EventEntry $eventEntry)
    {
        if ($this->player !== $eventEntry->getPlayer()) {
            return false;
        }

        if (!$eventEntry->isPlayStatusVerified()) {
            return false;
        }

        $this->achievements += $eventEntry->getAchievementsCnt() - $eventEntry->getAchievementsCntInitial();
        $this->minutes += $eventEntry->getPlayTime() - $eventEntry->getPlayTimeInitial();
        $this->beaten += (int)$eventEntry->isBeaten(false);

        return true;
    }

    /**
     * @return User
     */
    public function getPlayer(): User
    {
        return $this->player;
    }

    /**
     * @return ?int
     */
    public function getAchievements()
    {
        return $this->achievements;
    }

    /**
     * @return int
     * @Groups({"export"})
     */
    public function getMinutes(): int
    {
        return $this->minutes;
    }

    /**
     * @return ?int
     */
    public function getBeaten()
    {
        return $this->beaten;
    }

    /**
     * @return float
     * @Groups({"export"})
     */
    public function getHours()
    {
        return round($this->minutes / 60, 1);
    }
}
