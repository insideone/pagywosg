<?php

namespace App\DependencyInjection\Compiler;

use App\Security\PermissionsCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PermissionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(PermissionsCollection::class)) {
            return;
        }

        $definition = $container->getDefinition(PermissionsCollection::class);
        foreach (array_keys($container->findTaggedServiceIds('rbac.permission.enum')) as $id) {
            $definition->addMethodCall('addHolder', [new Reference($id)]);
        }
    }
}
