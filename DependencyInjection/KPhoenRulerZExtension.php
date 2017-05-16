<?php

namespace KPhoen\RulerZBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class KPhoenRulerZExtension extends Extension
{
    private $supportedTargets = ['native', 'doctrine', 'eloquent', 'pomm', 'elastica', 'elasticsearch'];

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('rulerz.yml');
        $loader->load('validators.yml');

        if (!is_bool($config['debug'])) {
            $parameterBag = $container->getParameterBag();
            $config['debug'] = $parameterBag->has('kernel.debug') ? $parameterBag->get('kernel.debug') : true;
        }

        if ($config['debug']) {
            $loader->load('debug.yml');
        }

        $this->configureCache($container, $config);
        $this->configureTargets($loader, $config);
    }

    private function configureCache(ContainerBuilder $container, array $config)
    {
        $directory = $container->getParameterBag()->resolveValue($config['cache']);
        $container->setParameter('rulerz.cache_directory', $directory);

        if (!file_exists($directory) && !@mkdir($directory, 0777, true)) {
            throw new \RuntimeException(sprintf('Could not create cache directory "%s".', $directory));
        }
    }

    private function configureTargets(YamlFileLoader $loader, array $config)
    {
        foreach ($this->supportedTargets as $target) {
            if ($config['targets'][$target]) {
                $loader->load(sprintf('targets/%s.yml', $target));
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
