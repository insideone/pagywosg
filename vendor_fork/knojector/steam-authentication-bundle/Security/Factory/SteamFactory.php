<?php

namespace Knojector\SteamAuthenticationBundle\Security\Factory;

use Knojector\SteamAuthenticationBundle\Security\Authentication\Provider\SteamProvider;
use Knojector\SteamAuthenticationBundle\Security\Firewall\SteamListener;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
class SteamFactory implements SecurityFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.steam.'.$id;
        $container
            ->setDefinition($providerId, new ChildDefinition(SteamProvider::class))
            ->replaceArgument(0, new Reference($userProvider))
        ;

        $listenerId = 'security.authentication.listener.steam.'.$id;
        $container->setDefinition($listenerId, new ChildDefinition(SteamListener::class));

        return [$providerId, $listenerId, $defaultEntryPoint];
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'steam';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $builder)
    {
    }
}