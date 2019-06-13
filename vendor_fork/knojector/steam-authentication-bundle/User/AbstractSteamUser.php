<?php

namespace Knojector\SteamAuthenticationBundle\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\Role;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
abstract class AbstractSteamUser implements SteamUserInterface, UserInterface
{
    /**
     * @var string
     *
     * @ORM\Column(type="bigint")
     */
    protected $steamId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $communityVisibilityState;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $profileState;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $profileName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $lastLogOff;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $commentPermission;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $profileUrl;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $avatar;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $personaState;

    /**
     * @var int|null
     *
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $primaryClanId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $joinDate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $countryCode;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    protected $roles;

    /**
     * {@inheritdoc}
     */
    public function getSteamId(): int
    {
        return $this->steamId;
    }

    /**
     * {@inheritdoc}
     */
    public function setSteamId(int $steamId)
    {
        $this->steamId = $steamId;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommunityVisibilityState(): int
    {
        return $this->communityVisibilityState;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommunityVisibilityState(int $state)
    {
        $this->communityVisibilityState = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileState(): int
    {
        return $this->profileState;
    }

    /**
     * {@inheritdoc}
     */
    public function setProfileState(int $state)
    {
        $this->profileState = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileName(): string
    {
        return $this->profileName;
    }

    /**
     * {@inheritdoc}
     */
    public function setProfileName(string $name)
    {
        $this->profileName = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastLogOff(): \DateTime
    {
        return $this->lastLogOff;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLogOff(int $lastLogOff)
    {
        $lastLogOffDate = new \DateTime();
        $lastLogOffDate->setTimestamp($lastLogOff);
        $this->lastLogOff = $lastLogOffDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentPermission(): int
    {
        return $this->commentPermission;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentPermission(int $permission)
    {
        $this->commentPermission = $permission;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileUrl(): string
    {
        return $this->profileUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setProfileUrl(string $url)
    {
        $this->profileUrl = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvatar(string $avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersonaState(): int
    {
        return $this->personaState;
    }

    /**
     * {@inheritdoc}
     */
    public function setPersonaState(? int $state)
    {
        $this->personaState = $state;
    }

    /**
     * @return int|null
     */
    public function getPrimaryClanId(): ?int
    {
        return $this->primaryClanId;
    }

    /**
     * @param int|null $primaryClanId
     */
    public function setPrimaryClanId(?int $primaryClanId): void
    {
        $this->primaryClanId = $primaryClanId;
    }

    /**
     * {@inheritdoc}
     */
    public function getJoinDate(): ?\DateTime
    {
        return $this->joinDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setJoinDate(?int $joinDate): void
    {
        if (null !== $joinDate) {
            $joinDateDate = new \DateTime();
            $joinDateDate->setTimestamp($joinDate);
            $joinDate = $joinDateDate;
        }

        $this->joinDate = $joinDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
       return $this->steamId;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        return;
    }
    
    /**
     * @return array
     */
    public function getRoles(): array {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = new Role($role);
        }

        return $roles;
    }

    /**
     * @param array $userData
     */
    public function update(array $userData)
    {
        $this->setCommunityVisibilityState($userData['communityvisibilitystate']);
        $this->setProfileState($userData['profilestate']);
        $this->setProfileName($userData['personaname']);
        $this->setLastLogOff($userData['lastlogoff']);
        $this->setCommentPermission($userData['commentpermission']);
        $this->setProfileUrl($userData['profileurl']);
        $this->setAvatar($userData['avatarfull']);
        $this->setPersonaState($userData['personastate']);
        $this->setPrimaryClanId(
            isset($userData['primaryclanid']) ? $userData['primaryclanid'] : null
        );
        $this->setCountryCode(
            isset($userData['loccountrycode']) ? $userData['loccountrycode'] : null
        );
    }
}
