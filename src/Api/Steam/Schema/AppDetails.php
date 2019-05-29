<?php

namespace App\Api\Steam\Schema;

class AppDetails
{
    private $steamAppid;

    private $achievements;

    /**
     * @return mixed
     */
    public function getSteamAppid()
    {
        return $this->steamAppid;
    }

    /**
     * @param mixed $steamAppid
     * @return AppDetails
     */
    public function setSteamAppid($steamAppid)
    {
        $this->steamAppid = $steamAppid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAchievements()
    {
        return $this->achievements;
    }

    /**
     * @param mixed $achievements
     * @return AppDetails
     */
    public function setAchievements($achievements)
    {
        $this->achievements = $achievements;
        return $this;
    }

    public function getAchievementsCount()
    {
        return $this->achievements['total'];
    }
}
