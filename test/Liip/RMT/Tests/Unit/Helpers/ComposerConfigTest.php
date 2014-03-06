<?php

namespace Liip\RMT\Tests\Unit\Helpers;

use Liip\RMT\Helpers\ComposerConfig;

/**
 * Tests the ComposerConfig helper.
 * 
 */
class ComposerConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     * system under test
     * 
     * @var \Liip\RMT\Helpers\ComposerConfig
     */
    private $helper;

    /**
     * Test setup
     * 
     */
    public function setUp()
    {
        parent::setUp();
        $context = new \Liip\RMT\Context();
        copy(__DIR__ . '/testdata/composer.json', sys_get_temp_dir() . '/composer.json');
        $context->setParameter('project-root', sys_get_temp_dir());

        $this->helper = new ComposerConfig($context);
    }

    /**
     * Ensures the constructor checks that the file exists.
     */
    public function testConstructorException()
    {
        $context = new \Liip\RMT\Context();

        $this->setExpectedException("\InvalidArgumentException");
        $this->helper = new ComposerConfig($context);
    }

    /**
     * Ensures that the contents of the extra/rmt  section are returned.
     */
    public function testGetRmtConfigSection()
    {
        $config = $this->helper->getRMTConfigSection();
        $this->assertInstanceOf('\Liip\RMT\Config', $config);
        $this->assertEquals('git', $config->getVcs());
    }

    /**
     * Ensures that null is returned if the config does not contain an rmt section
     */
    public function testGetRmtConfigSectionFails()
    {
        copy(__DIR__ . '/testdata/composer_no_rmt.json', sys_get_temp_dir() . '/composer.json');
        $this->assertNull($this->helper->getRMTConfigSection());
    }

    /**
     * Ensures that the version string is replaced.
     */
    public function testSetVersion()
    {
        $newVersion = 'abc';
        $this->helper->setVersion($newVersion);

        $contents = file_get_contents(sys_get_temp_dir() . '/composer.json');
        $this->assertContains('"version": "abc"', $contents);
    }

    /**
     * Ensures that the rmt config section can be added to the composer file.
     */
    public function testAddRmtConfigSection()
    {
        $config = \Liip\RMT\Config::create(array(
            'vcs' => 'git',
            "prerequisites" => array("working-copy-check", "display-last-changes"),
            'versionPersister' => "vcs-tag",
        ));
        $serialized = $this->helper->addRMTConfigSection($config);
        $this->assertContains('vcs-tag', $serialized);
        
        $config = $this->helper->getRMTConfigSection();
        $this->assertNotNull($config);
        $this->assertEquals('git', $config->getVcs(), var_export($config, true));
    }

    /**
     * Ensures that the current version can be read.
     */
    public function testGetCurrentVersion()
    {
        $this->assertEquals("0.2.0", $this->helper->getCurrentVersion());
    }
    
    public function testGetCurrentVersionFails()
    {
        copy(__DIR__ . '/testdata/composer_noversion.json', sys_get_temp_dir() . '/composer.json');
        $context = new \Liip\RMT\Context();
        $context->setParameter('project-root', sys_get_temp_dir());

        $this->helper = new ComposerConfig($context);
        
        $this->assertNull($this->helper->getCurrentVersion());
    }
 
    /**
     * 
     */
    public function testSavePreventsReplacementOfEmptyProperties()
    {
        $tmpFile = tempnam(sys_get_temp_dir(), '');
        copy(__DIR__ .'/testdata/empty.json', $tmpFile);
        $this->helper->setComposerFile($tmpFile);
        $this->helper->setVersion(new \Liip\RMT\Version('1.2.3'));
        
        $contents = file_get_contents($tmpFile);
        $this->assertNotContains('_empty_', $contents);
    }
    
    public function testGetProjectName()
    {
        $this->assertEquals('bonndan/ReleaseManager', $this->helper->getProjectName());
    }
}

