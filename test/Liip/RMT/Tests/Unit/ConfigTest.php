<?php
namespace Liip\RMT\Tests\Unit;

use Liip\RMT\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
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