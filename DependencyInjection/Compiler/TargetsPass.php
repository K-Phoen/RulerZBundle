<?php

namespace KPhoen\RulerZBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TargetsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $engineDefinition = $container->getDefinition('rulerz');

        foreach ($container->findTaggedServiceIds('rulerz.target') as $id => $attributes) {
            $engineDefinition->addMethodCall('registerCompilationTarget', [new Reference($id)]);
        }
    }
}
