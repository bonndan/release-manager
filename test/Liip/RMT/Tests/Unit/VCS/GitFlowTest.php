<?php

namespace Liip\RMT\Tests\Unit\Version;

use Liip\RMT\VCS\GitFlow;

/**
 * Tests the gitflow vcs
 * 
 * 
 */
class GitFlowTest extends \PHPUnit_Framework_TestCase
{
    protected $testDir;
    
    /**
     * system under test
     * @var \Liip\RMT\VCS\GitFlow
     */
    private $gitFlow;

    protected function setUp()
    {
        // Create a temp folder and extract inside the git test folder
        $tempDir = tempnam(sys_get_temp_dir(),'');
        if (file_exists($tempDir)) {
            unlink($tempDir);
        }
        mkdir($tempDir);
        chdir($tempDir);
        exec('unzip '.__DIR__.'/gitRepo.zip');
        exec('git checkout .');
        $this->testDir = $tempDir;
        
        $this->gitFlow = new GitFlow();
        $this->gitFlow->setDryRun(true);
    }
    
    public function testStartRelease()
    {
        $version = new \Liip\RMT\Version('2.2.2');
        
        $cmd = $this->gitFlow->startRelease($version);
        $this->assertEquals('flow release start 2.2.2', $cmd);
    }
    
    public function testFinishRelease()
    {
        system("git flow init -fd 1>/dev/null 2>&1");
        system("git flow release start 2.2.2 1>/dev/null 2>&1");
        
        $cmd = $this->gitFlow->finishRelease('test');
        $this->assertEquals('flow release finish -F -m "test" 2.2.2', $cmd);
    }
    
    public function testFinishReleaseException()
    {
        system("git flow init -fd 1>/dev/null 2>&1");
        
        $this->setExpectedException("\Liip\RMT\Exception", "Expected to find");
        $this->gitFlow->finishRelease('test');
    }
    
    public function testStartHotfix()
    {
        $version = new \Liip\RMT\Version('2.2.2');
        
        $cmd = $this->gitFlow->startHotfix($version);
        $this->assertEquals('flow hotfix start 2.2.2', $cmd);
    }
    
    public function testFinishHotfix()
    {
        system("git flow init -fd 1>/dev/null 2>&1");
        system("git flow hotfix start 2.2.2 1>/dev/null 2>&1");
        
        $cmd = $this->gitFlow->finishHotfix('test');
        $this->assertEquals('flow hotfix finish -F -m "test" 2.2.2', $cmd);
    }
    
    public function testFinishHotfixException()
    {
        system("git flow init -fd 1>/dev/null 2>&1");
        
        $this->setExpectedException("\Liip\RMT\Exception", "Expected to find");
        $this->gitFlow->finishRelease('test');
    }

    protected function tearDown()
    {
        // Remove the test folder
        exec('rm -rf '.$this->testDir);
        chdir(__DIR__);
    }

}
