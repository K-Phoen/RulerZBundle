<?php

declare(strict_types=1);

namespace KPhoen\RulerZBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class KPhoenRulerZExtension extends Extension
{
    public const SUPPORTED_TARGETS = [
        'native' => true, // enabled by default
        'doctrine' => false,
        'doctrine_dbal' => false,
        'eloquent' => false,
        'pomm' => false,
        'solarium' => false,
        'elasticsearch' => false,
    ];

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('rulerz.yml');
        $loader->load('forms.yml');
        $loader->load('validators.yml');

        if ($config['debug']) {
            $loader->load('debug.yml');
        }

        $this->configureCache($container, $config);
        $this->configureTargets($loader, $config);
    }

    private function configureCache(ContainerBuilder $container, array $config): void
    {
        $directory = $container->getParameterBag()->resolveValue($config['cache']);
        $container->setParameter('rulerz.cache_directory', $directory);

        if (!file_exists($directory) && !@mkdir($directory, 0777, true)) {
            throw new \RuntimeException(sprintf('Could not create cache directory "%s".', $directory));
        }
    }

    private function configureTargets(YamlFileLoader $loader, array $config): void
    {
        foreach (array_keys(self::SUPPORTED_TARGETS) as $target) {
            if ($config['targets'][$target]) {
                $loader->load(sprintf('targets/%s.yml', $target));
            }
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration($container->getParameter('kernel.debug'));
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'rulerz';
    }
}
