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
        copy( __DIR__ . '/testdata/composer.json', sys_get_temp_dir() . '/composer.json');
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
    
    public function testGetRmtConfigSection()
    {
        
    }
    
    public function testReplaceVersion()
    {
        $newVersion = 'abc';
        $this->helper->replaceVersion($newVersion);
        
        $contents = file_get_contents(sys_get_temp_dir() . '/composer.json');
        $this->assertContains('"version": "abc"', $contents);
    }
}

