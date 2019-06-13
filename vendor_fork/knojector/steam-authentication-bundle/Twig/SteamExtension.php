<?php

namespace Knojector\SteamAuthenticationBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SteamExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $loginRoute;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     * @param string          $loginRoute
     */
    public function __construct(
        RouterInterface $router,
        string $loginRoute
    )
    {
        $this->router = $router;
        $this->loginRoute = $loginRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('steam_authentication_route', [$this, 'getSteamAuthenticationRoute']),
        ];
    }

    /**
     * @return string
     */
    public function getSteamAuthenticationRoute(): string
    {
        return $this->router->generate($this->loginRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
