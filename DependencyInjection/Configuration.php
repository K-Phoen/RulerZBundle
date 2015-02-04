<?php

namespace KPhoen\RulerZBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('rulerz');

        $this->addCacheNode($rootNode);

        return $treeBuilder;
    }

    protected function addCacheNode(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('cache')
                    ->children()
                        ->scalarNode('provider')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('lifetime')
                            ->defaultValue(86400)
                            ->validate()
                            ->ifTrue(function ($v) { return !is_integer($v); })
                            ->thenInvalid('Only integer are allowed!')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }
}
