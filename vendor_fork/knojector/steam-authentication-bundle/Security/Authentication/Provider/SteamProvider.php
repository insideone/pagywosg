<?php

namespace Knojector\SteamAuthenticationBundle\Security\Authentication\Provider;

use Knojector\SteamAuthenticationBundle\Security\Authentication\Token\SteamUserToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
class SteamProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @param UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        $authenticatedToken = new SteamUserToken();
        $authenticatedToken->setUser($user);
        $authenticatedToken->setUsername($user->getUsername());
        $authenticatedToken->setAuthenticated(true);

        return $authenticatedToken;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof SteamUserToken;
    }
}