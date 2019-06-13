<?php

namespace Knojector\SteamAuthenticationBundle\DependencyInjection;

use Knojector\SteamAuthenticationBundle\Security\Authentication\Validator\RequestValidator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('knojector_steam_authentication');

        $rootNode
            ->children()
                ->scalarNode('api_key')->end()
                ->scalarNode('login_route')->end()
                ->scalarNode('login_redirect')->end()
                ->scalarNode('user_class')->end()
                ->scalarNode('request_validator_class')->defaultValue(RequestValidator::class)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
