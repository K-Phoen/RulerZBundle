<?php

namespace KPhoen\RulerZBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clear the cache.
 *
 * @author Kévin Gomez <contact@kevingomez.fr>
 */
class CacheClearCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rulerz:cache:clear')
            ->setDescription('Clear the cache')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cacheDir   = $this->getContainer()->getParameter('rulerz.cache_directory');
        $filesystem = $this->getContainer()->get('filesystem');

        if (!is_writable($cacheDir)) {
            throw new \RuntimeException(sprintf('Unable to write in the "%s" directory', $cacheDir));
        }

        if ($filesystem->exists($cacheDir)) {
            $filesystem->remove($cacheDir);
            $filesystem->mkdir($cacheDir);
        }
    }
}
