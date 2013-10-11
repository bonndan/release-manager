<?php

namespace Liip\RMT\Tests\Unit\Changelog;

use Liip\RMT\Changelog\Changelog;

class ChangelogTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorCreatesFile()
    {
        $file = sys_get_temp_dir(). '/test.xml';
        @unlink($file);
        $this->assertFileNotExists($file);
        $changelog = new Changelog($file);
        $this->assertFileExists($file);
        @unlink($file);
    }
    
    public function testVersionsAreReturned()
    {
        $file = __DIR__ . '/changelog.xml';
        $changelog = new Changelog($file);
        $versions = $changelog->getVersions();
        $this->assertInstanceOf("\DomNodeList", $versions);
        $this->assertEquals(2, $versions->length);
        
        $version1 = $versions->item(0);
        $this->assertEquals("0.0.0", $version1->attributes->getNamedItem('version')->nodeValue);
        $this->assertEquals("First version", $version1->getElementsByTagName('title')->item(0)->nodeValue);
    }
    
    public function testGetCurrentVersion()
    {
        $file = __DIR__ . '/changelog.xml';
        $changelog = new Changelog($file);
        $current = $changelog->getCurrentVersion();
        $this->assertEquals('0.1.0', $current);
    }
    
    public function testGetCurrentVersionFails()
    {
        $file = sys_get_temp_dir(). '/test.xml';
        @unlink($file);
        $changelog = new Changelog($file);
        $current = $changelog->getCurrentVersion();
        $this->assertNull($current);
    }
    
    public function testAddVersion()
    {
        $file = sys_get_temp_dir(). '/test.xml';
        @unlink($file);
        
        $changelog = new Changelog($file);
        $commits = array(
            'myhash' => 'my message',
            'abc' => 'def',
        );
        $changelog->addVersion('0.1.0', 'First version', $commits);
        
        $this->assertContains('version="0.1.0"', file_get_contents($file));
        $versions = $changelog->getVersions();
    }
}
