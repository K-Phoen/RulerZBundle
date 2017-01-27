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
            'cache' => $this->root->url().'/cache',
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
        $this->assertFalse(is_dir($this->root->url().'/cache'));

        $this->load();

        $this->assertTrue(is_dir($this->root->url().'/cache'));
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Could not create cache directory
     */
    public function testItThrowsIfTheCacheDirectoryCanNotBeCreated()
    {
        $this->root = vfsStream::setup('rulerz_bundle', 0);

        $this->load();
    }

    public function testItLoadsTargetsDefinedInTheConfig()
    {
        $this->load([
            'targets' => [
                'pomm' => null,
                'doctrine' => null,
            ],
        ]);

        $this->assertContainerBuilderHasService('rulerz.compilation_target.pomm');
        $this->assertContainerBuilderHasService('rulerz.compilation_target.doctrine');
        $this->assertContainerBuilderNotHasService('rulerz.compilation_target.elastica');
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUnknownTargetsCantBeLoaded()
    {
        $this->load([
            'targets' => [
                'unknown' => null,
            ],
        ]);
    }
}
