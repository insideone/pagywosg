<?php

namespace Knojector\SteamAuthenticationBundle;

use Knojector\SteamAuthenticationBundle\Security\Factory\SteamFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
class KnojectorSteamAuthenticationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new SteamFactory());
    }
}