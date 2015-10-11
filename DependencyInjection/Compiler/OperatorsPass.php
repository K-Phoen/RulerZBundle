<?php

namespace KPhoen\RulerZBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class OperatorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $executors = $this->getExecutors($container);

        foreach ($container->findTaggedServiceIds('rulerz.operator') as $id => $attributesSet) {
            foreach ($attributesSet as $attributes) {
                if (empty($executors[$attributes['executor']])) {
                    continue;
                }

                $executors[$attributes['executor']]->addMethodCall('setOperator', [
                    $attributes['operator'], new Reference($id)
                ]);
            }
        }
    }

    private function getExecutors(ContainerBuilder $container)
    {
        $executors = [];

        foreach ($container->findTaggedServiceIds('rulerz.executor') as $id => $attributes) {
            $executor = $container->getDefinition($id);

            $executors[$executor->getClass()] = $executor;
        }

        return $executors;
    }
}
