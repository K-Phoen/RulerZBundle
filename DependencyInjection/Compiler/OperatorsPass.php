<?php

declare(strict_types=1);

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
                if ($container->hasDefinition($target = 'rulerz.target.'.$attributes['target'])) {
                    $targetDefinition = $container->getDefinition($target);
                } elseif ($container->hasDefinition($attributes['target'])) {
                    $targetDefinition = $container->getDefinition($attributes['target']);
                } else {
                    throw new \LogicException('Unable to find service definition for compilation target: '.$attributes['target']);
                }

                if (!empty($attributes['inline']) && $attributes['inline']) {
                    $targetDefinition->addMethodCall('defineInlineOperator', [
                        $attributes['operator'], new Reference($id),
                    ]);
                } else {
                    $targetDefinition->addMethodCall('defineOperator', [
                        $attributes['operator'], new Reference($id),
                    ]);
                }
            }
        }
    }
}
