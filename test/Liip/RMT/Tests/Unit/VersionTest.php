<?php
namespace Liip\RMT\Tests\Unit;

use Liip\RMT\Version;

/**
 * Test the config value object.
 * 
 * 
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInitial()
    {
        $version = new Version(Version::INITIAL);
        $this->assertTrue($version->isInitial());
    }
    
    public function testIsNotInitial()
    {
        $version = new Version('1.2.3');
        $this->assertFalse($version->isInitial());
    }
    
    public function testCreateInitialVersion()
    {
        $version = Version::createInitialVersion();
        $this->assertInstanceOf("Liip\RMT\Version", $version);
    }
}