<?php

namespace App\Api\Steam;

use Doctrine\ORM\EntityManagerInterface;
use Knojector\SteamAuthenticationBundle\Exception\InvalidApiResponseException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidUserClassException;
use Knojector\SteamAuthenticationBundle\Factory\UserFactory;
use Knojector\SteamAuthenticationBundle\Http\SteamApiClient;
use Knojector\SteamAuthenticationBundle\User\SteamUserInterface;

class PlayerSummariesProvider
{
    /**
     * @var SteamApiClient
     */
    protected $apiClient;
    /**
     * @var UserFactory
     */
    protected $userFactory;

    /** @var EntityManagerInterface */
    protected $em;

    public function __construct(SteamApiClient $steamApiClient, UserFactory $userFactory)
    {
        $this->apiClient = $steamApiClient;
        $this->userFactory = $userFactory;
    }

    /**
     * @param $steamId
     * @return SteamUserInterface|null
     * @throws InvalidApiResponseException
     * @throws InvalidUserClassException
     */
    public function fetch(int $steamId)
    {
        if (!$steamId) {
            return null;
        }

        $profileData = $this->apiClient->loadProfile($steamId);
        if (!$profileData) {
            return null;
        }

        $profileData['commentpermission'] = $profileData['commentpermission'] ?? 0;

        return $this->userFactory->getFromSteamApiResponse($profileData);
    }
}
