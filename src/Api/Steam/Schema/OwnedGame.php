<?php

namespace App\Api\Steam\Schema;

class OwnedGame
{
    /** @var int */
    protected $appId;

    /** @var string */
    protected $name;

    /** @var int */
    protected $playtimeForever;

    /** @var bool */
    protected $communityVisibleStats;

    /**
     * @return int
     */
    public function getAppId(): int
    {
        return $this->appId;
    }

    /**
     * @param int $appId
     * @return OwnedGame
     */
    public function setAppId(int $appId): OwnedGame
    {
        $this->appId = $appId;
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
     * @return OwnedGame
     */
    public function setName(string $name): OwnedGame
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasCommunityVisibleStats(): bool
    {
        return $this->communityVisibleStats;
    }

    /**
     * @param bool $communityVisibleStats
     * @return OwnedGame
     */
    public function setCommunityVisibleStats(bool $communityVisibleStats): OwnedGame
    {
        $this->communityVisibleStats = $communityVisibleStats;
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
     * @return OwnedGame
     */
    public function setPlaytimeForever(int $playtimeForever): OwnedGame
    {
        $this->playtimeForever = $playtimeForever;
        return $this;
    }
}
