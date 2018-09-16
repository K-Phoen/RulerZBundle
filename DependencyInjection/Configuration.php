<?php

declare(strict_types=1);

namespace KPhoen\RulerZBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $debug;

    public function __construct(bool $debug)
    {
        $this->debug = $debug;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('rulerz');

        $this->addCacheConfig($rootNode);
        $this->addDebugConfig($rootNode);
        $this->addTargetsConfig($rootNode);

        return $treeBuilder;
    }

    private function addCacheConfig(ArrayNodeDefinition $rootNode): ArrayNodeDefinition
    {
        $rootNode
            ->children()
                ->scalarNode('cache')->defaultValue('%kernel.cache_dir%/rulerz')->end()
            ->end();

        return $rootNode;
    }

    private function addDebugConfig(ArrayNodeDefinition $rootNode): ArrayNodeDefinition
    {
        $rootNode
            ->children()
                ->booleanNode('debug')->defaultValue($this->debug)->end()
            ->end();

        return $rootNode;
    }

    private function addTargetsConfig(ArrayNodeDefinition $rootNode): ArrayNodeDefinition
    {
        $rootNode
            ->children()
                ->arrayNode('targets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('native')->defaultTrue()->end()
                        ->booleanNode('doctrine')->defaultFalse()->end()
                        ->booleanNode('doctrine_dbal')->defaultFalse()->end()
                        ->booleanNode('eloquent')->defaultFalse()->end()
                        ->booleanNode('pomm')->defaultFalse()->end()
                        ->booleanNode('elastica')->defaultFalse()->end()
                        ->booleanNode('elasticsearch')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }
}
