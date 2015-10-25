<?php

namespace Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use org\bovigo\vfs\vfsStream;

use KPhoen\RulerZBundle\DependencyInjection\KPhoenRulerZExtension;

class KPhoenRulerZExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $root;

    public function setUp()
    {
        parent::setUp();

        $this->root = vfsStream::setup('rulerz_bundle');
    }

    protected function getContainerExtensions()
    {
        return [new KPhoenRulerZExtension()];
    }

    protected function getMinimalConfiguration()
    {
        return [
            'cache' => $this->root->url() . '/cache',
        ];
    }

    public function testItLoadsRulerz()
    {
        $this->load();

        $this->assertContainerBuilderHasService('rulerz', 'RulerZ\RulerZ');
    }

    public function testItLoadsValidators()
    {
        $this->load();

        $this->assertContainerBuilderHasService('rulerz.validator.unique.rule_validator', 'KPhoen\RulerZBundle\Validator\Constraints\RuleValidator');
    }

    public function testItCreatesTheCacheDirectory()
    {
        $this->assertFalse(is_dir($this->root->url() . '/cache'));

        $this->load();

        $this->assertTrue(is_dir($this->root->url() . '/cache'));
    }

    public function testItLoadsExecutorsDefinedInTheConfig()
    {
        $this->load([
            'executors' => [
                'pomm'     => null,
                'doctrine' => null,
            ]
        ]);

        $this->assertContainerBuilderHasService('rulerz.executor.pomm');
        $this->assertContainerBuilderHasService('rulerz.executor.doctrine');
        $this->assertContainerBuilderNotHasService('rulerz.executor.elastica');
    }

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUnknownExecutorsCantBeLoaded()
    {
        $this->load([
            'executors' => [
                'unknown' => null,
            ]
        ]);
    }
}
