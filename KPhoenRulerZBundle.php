<?php

namespace KPhoen\RulerZBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use KPhoen\RulerZBundle\DependencyInjection\Compiler\ExecutorsPass;
use KPhoen\RulerZBundle\DependencyInjection\Compiler\OperatorsPass;

class KPhoenRulerZBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ExecutorsPass());
        $container->addCompilerPass(new OperatorsPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new DependencyInjection\KPhoenRulerZExtension();
    }
}
