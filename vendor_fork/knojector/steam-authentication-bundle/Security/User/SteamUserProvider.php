<?php

namespace Knojector\SteamAuthenticationBundle\Security\User;

use Knojector\SteamAuthenticationBundle\User\SteamUserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knojector\SteamAuthenticationBundle\Factory\UserFactory;
use Knojector\SteamAuthenticationBundle\Http\SteamApiClient;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
class SteamUserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SteamApiClient
     */
    private $api;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SteamApiClient         $steamApiClient
     * @param string                 $userClass
     * @param UserFactory            $userFactory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SteamApiClient $steamApiClient,
        string $userClass,
        UserFactory $userFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->api = $steamApiClient;
        $this->userClass = $userClass;
        $this->userFactory = $userFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->entityManager->getRepository($this->userClass)->findOneBy(['steamId' => $username]);
        $userData = $this->api->loadProfile($username);
        if (null === $user) {
            $user = $this->userFactory->getFromSteamApiResponse($userData);

            $this->entityManager->persist($user);
        } else {
            $user->update($userData);
        }

        $this->entityManager->flush();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SteamUserInterface) {
            throw new UnsupportedUserException();
        }

        return $this->entityManager->getRepository($this->userClass)->findOneBy(['steamId' => $user->getSteamId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->userClass;
    }
}