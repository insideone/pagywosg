<?php

namespace Knojector\SteamAuthenticationBundle\Factory;

use Knojector\SteamAuthenticationBundle\Exception\InvalidUserClassException;
use Knojector\SteamAuthenticationBundle\User\SteamUserInterface;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
class UserFactory
{
    /**
     * @var string
     */
    private $userClass;

    /**
     * @param string $userClass
     */
    public function __construct(string $userClass)
    {
        $this->userClass = $userClass;
    }

    /**
     * @param array $userData
     *
     * @return SteamUserInterface
     *
     * @throws InvalidUserClassException
     */
    public function getFromSteamApiResponse(array $userData)
    {
        $user = new $this->userClass;
        if (!$user instanceof SteamUserInterface) {
            throw new InvalidUserClassException($this->userClass);
        }

        $user->setSteamId($userData['steamid']);
        $user->setCommunityVisibilityState($userData['communityvisibilitystate']);
        $user->setProfileState($userData['profilestate']);
        $user->setProfileName($userData['personaname']);
        $user->setLastLogOff($userData['lastlogoff']);
        $user->setCommentPermission($userData['commentpermission']);
        $user->setProfileUrl($userData['profileurl']);
        $user->setAvatar($userData['avatarfull']);
        $user->setPersonaState($userData['personastate']);
        $user->setPrimaryClanId(
            isset($userData['primaryclanid']) ? $userData['primaryclanid'] : null
        );
        $user->setJoinDate(
            isset($userData['timecreated']) ? $userData['timecreated'] : null
        );
        $user->setCountryCode(
            isset($userData['loccountrycode']) ? $userData['loccountrycode'] : null
        );

        return $user;
    }
}
