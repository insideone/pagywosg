<?php

namespace App\DependencyInjection\Compiler;

use App\Framework\Renderer\RendererProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RendererPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(RendererProvider::class)) {
            return;
        }

        $definition = $container->findDefinition(RendererProvider::class);
        foreach (array_keys($container->findTaggedServiceIds('app.renderer')) as $id) {
            $definition->addMethodCall('addRenderer', [new Reference($id)]);
        }
    }
}
