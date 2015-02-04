<?php

namespace KPhoen\RulerZBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class RulerZExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor     = new Processor();
        $configuration = new Configuration();
        $config        = $processor->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('rulerz.yml');

        if (!empty($config['cache'])) {
            $this->setupCache($config['cache'], $container);
        }
    }

    private function setupCache(array $config, ContainerBuilder $container)
    {
        $engineDefinition = $container->getDefinition('rulerz');
        $cachedInterpreterDefinition = $container->getDefinition('rulerz.interpreter.cached');

        $cachedInterpreterDefinition->replaceArgument(0, new Reference('rulerz.interpreter.hoa'));
        $cachedInterpreterDefinition->replaceArgument(1, new Reference($config['provider']));
        $cachedInterpreterDefinition->replaceArgument(2, $config['lifetime']);

        $engineDefinition->replaceArgument(0, new Reference('rulerz.interpreter.cached'));
    }
}
