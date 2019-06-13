<?php

namespace Knojector\SteamAuthenticationBundle\User;

/**
 * @see https://developer.valvesoftware.com/wiki/Steam_Web_API#GetPlayerSummaries_.28v0002.29
 *
 * @author Knojector <dev@404-labs.xyz>
 */
interface SteamUserInterface
{
    /**
     * @return int
     */
    public function getSteamId(): int;

    /**
     * @param int $steamId
     */
    public function setSteamId(int $steamId);

    /**
     * @return int
     */
    public function getCommunityVisibilityState(): int;

    /**
     * @param int $state
     */
    public function setCommunityVisibilityState(int $state);

    /**
     * @return int
     */
    public function getProfileState(): int;

    /**
     * @param int $state
     */
    public function setProfileState(int $state);

    /**
     * @return string
     */
    public function getProfileName(): string;

    /**
     * @param string $name
     */
    public function setProfileName(string  $name);

    /**
     * @return \DateTime
     */
    public function getLastLogOff(): \DateTime;

    /**
     * @param int $lastLogOff
     */
    public function setLastLogOff(int $lastLogOff);

    /**
     * @return int
     */
    public function getCommentPermission(): int;

    /**
     * @param int $permission
     */
    public function setCommentPermission(int $permission);

    /**
     * @return string
     */
    public function getProfileUrl(): string;

    /**
     * @param string $url
     */
    public function setProfileUrl(string $url);

    /**
     * @return string
     */
    public function getAvatar(): string;

    /**
     * @param string $avatar
     */
    public function setAvatar(string $avatar);

    /**
     * @return int
     */
    public function getPersonaState(): int;

    /**
     * @param int|null $state
     */
    public function setPersonaState(?int $state);

    /**
     * @return int|null
     */
    public function getPrimaryClanId(): ?int;

    /**
     * @param int $clanId
     */
    public function setPrimaryClanId(int $clanId);

    /**
     * @return \DateTime|null
     */
    public function getJoinDate(): ?\DateTime;

    /**
     * @param int|null $joinDate
     */
    public function setJoinDate(?int $joinDate);

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string;

    /**
     * @param string|null $countryCode
     */
    public function setCountryCode(?string $countryCode);

    /**
     * @param array $userData
     */
    public function update(array $userData);
}
