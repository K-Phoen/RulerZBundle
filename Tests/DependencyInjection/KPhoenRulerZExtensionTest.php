<?php

namespace Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use org\bovigo\vfs\vfsStream;

use KPhoen\RulerZBundle\DependencyInjection\KPhoenRulerZExtension;
use KPhoen\RulerZBundle\Validator\Constraints\RuleValidator;
use RulerZ\RulerZ;

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
        $this->setParameter('kernel.debug', true);
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

        $this->assertContainerBuilderHasService('rulerz', RulerZ::class);
    }

    public function testItLoadsValidators()
    {
        $this->load();

        $this->assertContainerBuilderHasService('rulerz.validator.unique.rule_validator', RuleValidator::class);
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

        $this->assertContainerBuilderHasService('rulerz.target.pomm');
        $this->assertContainerBuilderHasService('rulerz.target.doctrine');
        $this->assertContainerBuilderNotHasService('rulerz.target.elastica');
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
