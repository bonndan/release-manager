<?php

namespace Liip\RMT\Tests\Version\Detector;

use Liip\RMT\VCS\Git;
use Liip\RMT\Version\Detector\GitFlowBranch;
use PHPUnit_Framework_TestCase;

/**
 * Description of GitFlowBranchTest
 *
 * @author daniel
 */
class GitFlowBranchTest extends PHPUnit_Framework_TestCase
{

    protected $testDir;

    /**
     * system under test
     * @var GitFlowBranch 
     */
    protected $detector;

    protected function setUp()
    {
        // Create a temp folder and extract inside the git test folder
        $tempDir = tempnam(sys_get_temp_dir(), '');
        if (file_exists($tempDir)) {
            unlink($tempDir);
        }
        mkdir($tempDir);
        chdir($tempDir);
        exec('unzip ' . dirname(dirname(__DIR__)) . '/VCS/gitRepo.zip');
        exec('git checkout .');
        $this->testDir = $tempDir;

        $git = new Git();
        $this->detector = new GitFlowBranch($git, GitFlowBranch::RELEASE);
    }

    public function testGetCurrentReleaseVersion()
    {
        system("git flow init -fd 1>/dev/null 2>&1");
        system("git flow release start 2.2.2 1>/dev/null 2>&1");
        $version = $this->detector->getCurrentVersion();

        $this->assertEquals('2.2.2', $version->getVersion());
    }
    
    public function testGetCurrentHotfixVersion()
    {
        $this->detector = new GitFlowBranch(new Git(), GitFlowBranch::HOTFIX);
        system("git flow init -fd 1>/dev/null 2>&1");
        system("git flow hotfix start 2.2.2 1>/dev/null 2>&1");
        $version = $this->detector->getCurrentVersion();

        $this->assertEquals('2.2.2', $version->getVersion());
    }
    
    public function testGetCurrentHotfixVersionWithoutDefinedBranchType()
    {
        $this->detector = new GitFlowBranch(new Git());
        system("git flow init -fd 1>/dev/null 2>&1");
        system("git flow hotfix start 2.2.2 1>/dev/null 2>&1");
        $version = $this->detector->getCurrentVersion();

        $this->assertEquals('2.2.2', $version->getVersion());
        $this->assertEquals(GitFlowBranch::HOTFIX, $this->detector->getBranchType());
    }
    
    public function testGetCurrentReleaseVersionWithoutDefinedBranchType()
    {
        $this->detector = new GitFlowBranch(new Git());
        system("git flow init -fd 1>/dev/null 2>&1");
        system("git flow release start 2.2.2 1>/dev/null 2>&1");
        $version = $this->detector->getCurrentVersion();

        $this->assertEquals('2.2.2', $version->getVersion());
        $this->assertEquals(GitFlowBranch::RELEASE, $this->detector->getBranchType());
    }
    
    public function testGetCurrentVersionWithoutDefinedBranchTypeFails()
    {
        $this->detector = new GitFlowBranch(new Git());
        $this->setExpectedException("\Liip\RMT\Exception", "Cannot detect release or hotfix branch.");
        $this->detector->getCurrentVersion();
    }

    public function testFinishReleaseException()
    {
        system("git flow init -fd 1>/dev/null 2>&1");

        $this->setExpectedException("\Liip\RMT\Exception", "Expected to find");
        $this->detector->getCurrentVersion();
    }
    
    public function testIsInTheFlow()
    {
        system("git flow init -fd 1>/dev/null 2>&1");
        system("git flow hotfix start 2.2.2 1>/dev/null 2>&1");

        $this->assertTrue($this->detector->isInTheFlow());
    }
    
    public function testIsNotInTheFlow()
    {
        system("git flow init -fd 1>/dev/null 2>&1");

        $this->assertFalse($this->detector->isInTheFlow());
    }
}
