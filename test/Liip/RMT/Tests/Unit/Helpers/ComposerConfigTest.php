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
    public function testReplaceVersion()
    {
        $newVersion = 'abc';
        $this->helper->replaceVersion($newVersion);

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

}

