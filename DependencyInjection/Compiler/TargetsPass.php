<?php

declare(strict_types=1);

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
            $targetDefinition = $container->getDefinition($id);

            if (!class_exists($targetDefinition->getClass())) {
                throw new \RuntimeException(sprintf('Class not found for target "%s". Did you require the target\'s library?', $id));
            }

            $engineDefinition->addMethodCall('registerCompilationTarget', [new Reference($id)]);
        }
    }
}
