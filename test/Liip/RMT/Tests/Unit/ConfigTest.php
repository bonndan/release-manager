<?php
namespace Liip\RMT\Tests\Unit;

use Liip\RMT\Config;

/**
 * Test the config value object.
 * 
 * 
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the factory methods checks the keys.
     */
    public function testInvalidEntry()
    {
        $this->setExpectedException("\Liip\RMT\Config\Exception");
        Config::create(array('unknown' => 'key'));
    }
    
    public function testFactoryReturnsConfig()
    {
        $config = Config::create($this->getConfigData());
        
        $this->assertInstanceOf("\Liip\RMT\Config", $config);
        $this->assertEquals('git', $config->getVcs());
        $this->assertContains('working-copy-check', $config->getPrerequisites());
    }
    
    public function testToJson()
    {
        $config = Config::create($this->getConfigData());
        $json = $config->toJson();
        $this->assertEquals('git', $json->vcs);
        $this->assertContains('working-copy-check',$json->prerequisites);
    }
    
    protected function getConfigData()
    {
        return array(
            'vcs' => 'git',
            'prerequisites' => array('working-copy-check')
        );
    }
}