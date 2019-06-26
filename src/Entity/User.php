<?php

namespace App\Entity;

use App\Framework\Entity\IdentityProvider;
use App\Framework\Security\RoleEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Fxp\Component\Security\Model\UserInterface;
use Knojector\SteamAuthenticationBundle\User\SteamUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * We have to copy AbstractStemUser stuff because it conflicts with RBAC bundle
 * Also we can't use Groups annotation without copying properties
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table("users",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"steam_id"})}
 * )
 */
class User implements SteamUserInterface, UserInterface, IdentityProvider
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"export"})
     */
    private $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

    /**
     * @var string
     * @ORM\Column(name="steam_id", type="string", length=17)
     * @Groups({"export", "import-createEventEntry"})
     */
    protected $steamId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $communityVisibilityState;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $profileState;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     * @Groups({"export"})
     */
    protected $profileName;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=24, nullable=true)
     * @Groups({"export"})
     */
    protected $sgProfileName;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastLogOff;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $commentPermission;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=160)
     * @Groups({"export"})
     */
    protected $profileUrl;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=160)
     * @Groups({"export"})
     */
    protected $avatar;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $personaState;

    /**
     * @var int|null
     *
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $primaryClanId;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $joinDate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true, length=4)
     */
    protected $countryCode;

    /**
     * @var string[]
     * @ORM\Column(type="json")
     * @Groups({"export"})
     */
    protected $roles = [];

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

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
    public function setSteamId($steamId)
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
    public function setCommunityVisibilityState(?int $state)
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
    public function setProfileState(?int $state)
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
     * @return string|null
     */
    public function getSgProfileName(): ?string
    {
        return $this->sgProfileName;
    }

    /**
     * @param string $sgProfileName
     * @return User
     */
    public function setSgProfileName(string $sgProfileName): self
    {
        $this->sgProfileName = $sgProfileName;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastLogOff(): DateTime
    {
        return $this->lastLogOff;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLogOff(?int $lastLogOff)
    {
        $lastLogOffDate = new DateTime();
        $lastLogOffDate->setTimestamp($lastLogOff);
        $this->lastLogOff = $lastLogOffDate;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentPermission(): int
    {
        return (int)$this->commentPermission;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentPermission(?int $permission)
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
    public function setPersonaState(?int $state)
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
    public function getJoinDate(): ?DateTime
    {
        return $this->joinDate;
    }

    /**
     * {@inheritdoc}
     */
    public function setJoinDate(?int $joinDate): void
    {
        if (null !== $joinDate) {
            $joinDateDate = new DateTime();
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
     * @param array $userData
     */
    public function update(array $userData)
    {
        $this->setCommunityVisibilityState($userData['communityvisibilitystate']);
        $this->setProfileState($userData['profilestate']);
        $this->setProfileName($userData['personaname']);
        $this->setLastLogOff($userData['lastlogoff']);
        $this->setCommentPermission(
            isset($userData['commentpermission']) ? $userData['commentpermission'] : null
        );
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

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return $this->isAccountNonLocked();
    }

    public function isAccountNonLocked()
    {
        return !$this->isEnabled();
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role, false);
        }

        return $this;
    }

    public function addRole(string $role, $checkExists = true): self
    {
        if ($this->hasRole($role)) {
            return $this;
        }

        $this->roles[] = $role;
        return $this;
    }

    public function removeRole(string $role): self
    {
        $this->roles = array_values(array_diff($this->roles, [$role]));

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRoleNames()
    {
        return $this->getRoles();
    }

    public function isAdmin()
    {
        return $this->hasRole(RoleEnum::ADMIN);
    }
}
