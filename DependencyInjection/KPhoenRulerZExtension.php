<?php

namespace KPhoen\RulerZBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class KPhoenRulerZExtension extends Extension
{
    private $supportedExecutors = ['doctrine', 'eloquent', 'pomm', 'elastica', 'elasticsearch'];

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('rulerz.yml');
        $loader->load('validators.yml');

        if ($config['debug']) {
            $loader->load('debug.yml');
        }

        $this->configureCache($container, $config);
        $this->configureExecutors($loader, $config);
    }

    private function configureCache(ContainerBuilder $container, array $config)
    {
        $directory = $container->getParameterBag()->resolveValue($config['cache']);
        $container->setParameter('rulerz.cache_directory', $directory);

        if (!file_exists($directory) && !@mkdir($directory, 0777, true)) {
            throw new \RuntimeException(sprintf('Could not create cache directory "%s".', $directory));
        }
    }

    private function configureExecutors(YamlFileLoader $loader, array $config)
    {
        foreach ($this->supportedExecutors as $executor) {
            if ($config['executors'][$executor]) {
                $loader->load(sprintf('executors/%s.yml', $executor));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'kphoen_rulerz';
    }
}
