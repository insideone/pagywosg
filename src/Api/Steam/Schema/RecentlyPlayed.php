<?php

namespace App\Api\Steam\Schema;

class RecentlyPlayed
{
    /** @var int */
    protected $appid;

    /** @var string */
    protected $name;

    /** @var int */
    protected $playtime2weeks;

    /** @var int */
    protected $playtimeForever;

    /**
     * @return int
     */
    public function getAppid(): int
    {
        return $this->appid;
    }

    /**
     * @param int $appid
     * @return RecentlyPlayed
     */
    public function setAppid(int $appid): RecentlyPlayed
    {
        $this->appid = $appid;
        return $this;
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
     * @return RecentlyPlayed
     */
    public function setName(string $name): RecentlyPlayed
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlaytime2weeks(): int
    {
        return $this->playtime2weeks;
    }

    /**
     * @param int $playtime2weeks
     * @return RecentlyPlayed
     */
    public function setPlaytime2weeks(int $playtime2weeks): RecentlyPlayed
    {
        $this->playtime2weeks = $playtime2weeks;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlaytimeForever(): int
    {
        return $this->playtimeForever;
    }

    /**
     * @param int $playtimeForever
     * @return RecentlyPlayed
     */
    public function setPlaytimeForever(int $playtimeForever): RecentlyPlayed
    {
        $this->playtimeForever = $playtimeForever;
        return $this;
    }
}
