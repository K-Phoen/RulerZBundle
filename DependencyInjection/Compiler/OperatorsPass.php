<?php

namespace KPhoen\RulerZBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class OperatorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('rulerz.operator') as $id => $attributesSet) {
            foreach ($attributesSet as $attributes) {
                $executor = $container->getDefinition($attributes['compilation_target']);

                if (!empty($attributes['inline']) && $attributes['inline']) {
                    $executor->addMethodCall('defineInlineOperator', [
                        $attributes['operator'], new Reference($id),
                    ]);
                } else {
                    $executor->addMethodCall('defineOperator', [
                        $attributes['operator'], new Reference($id),
                    ]);
                }
            }
        }
    }
}
