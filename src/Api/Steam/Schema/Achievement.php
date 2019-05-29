<?php

namespace App\Api\Steam\Schema;

class Achievement
{
    /** @var int */
    protected $appid;

    /** @var string */
    protected $apiname;

    /** @var bool */
    protected $achieved;

    /** @var int */
    protected $unlocktime;

    /** @var string */
    protected $player;

    /**
     * @return int
     */
    public function getAppid(): int
    {
        return $this->appid;
    }

    /**
     * @param int $appid
     * @return Achievement
     */
    public function setAppid(int $appid): Achievement
    {
        $this->appid = $appid;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiname(): string
    {
        return $this->apiname;
    }

    /**
     * @param string $apiname
     * @return Achievement
     */
    public function setApiname(string $apiname): Achievement
    {
        $this->apiname = $apiname;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAchieved(): bool
    {
        return $this->achieved;
    }

    /**
     * @param bool $achieved
     * @return Achievement
     */
    public function setAchieved(bool $achieved): Achievement
    {
        $this->achieved = $achieved;
        return $this;
    }

    /**
     * @return int
     */
    public function getUnlocktime(): int
    {
        return $this->unlocktime;
    }

    /**
     * @param int $unlocktime
     * @return Achievement
     */
    public function setUnlocktime(int $unlocktime): Achievement
    {
        $this->unlocktime = $unlocktime;
        return $this;
    }

    public function setPlayer($stemId)
    {
        $this->player = $stemId;
        return $this;
    }
}
