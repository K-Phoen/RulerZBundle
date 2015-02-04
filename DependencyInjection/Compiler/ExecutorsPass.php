<?php

namespace KPhoen\RulerZBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ExecutorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $engineDefinition = $container->getDefinition('rulerz');

        foreach ($container->findTaggedServiceIds('rulerz.executor') as $id => $attributes) {
            $engineDefinition->addMethodCall('registerExecutor', array(new Reference($id)));
        }
    }
}
