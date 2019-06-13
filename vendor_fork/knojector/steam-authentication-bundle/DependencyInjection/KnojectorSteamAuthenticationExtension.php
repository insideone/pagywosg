<?php

namespace Knojector\SteamAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
class KnojectorSteamAuthenticationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('knojector.steam_authentication.api_key', $config['api_key']);
        $container->setParameter('knojector.steam_authentication.login_route', $config['login_route']);
        $container->setParameter('knojector.steam_authentication.login_redirect', $config['login_redirect']);
        $container->setParameter('knojector.steam_authentication.user_class', $config['user_class']);

        if (isset($config['request_validator_class'])) {
            $container->setParameter(
                'knojector.steam_authentication.request_validator_class',
                $config['request_validator_class']
            );
        }
    }
}
