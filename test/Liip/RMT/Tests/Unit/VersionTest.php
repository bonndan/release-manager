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
    
    /**
     * @dataProvider versionProvider
     */
    public function testGetDifferenceType(Version $higherVersion, $expected)
    {
        $version = new Version('1.0.0');
        $result = $version->getDifferenceType($higherVersion);
        $this->assertEquals($expected, $result);
    }
    
    public function versionProvider()
    {
        return array(
            array(new Version('1.0.0'), null),
            array(new Version('1.0.0-2'), Version::TYPE_BUILD),
            array(new Version('1.0.1'), Version::TYPE_PATCH),
            array(new Version('1.1.1'), Version::TYPE_MINOR),
            array(new Version('2.1.3'), Version::TYPE_MAJOR),
        );
    }
}