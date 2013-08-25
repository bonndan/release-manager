<?php
namespace Liip\RMT\Tests\Unit;

use Liip\RMT\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryReturnsConfig()
    {
        $config = Config::create(array('vcs' => 'git'));
        
        $this->assertInstanceOf("\Liip\RMT\Config", $config);
        $this->assertEquals('git', $config->getVcs());
    }
}