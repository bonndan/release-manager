<?php

namespace Liip\RMT\Tests\Version\Detector;

use Liip\RMT\VCS\Git;
use Liip\RMT\Version\Detector\GitFlowReleaseBranch;
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
     * @var GitFlowReleaseBranch 
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
        $this->detector = new GitFlowReleaseBranch($git);
    }

    public function testGetCurrentVersion()
    {
        system("git flow init -fd");
        system("git flow release start 2.2.2");
        $version = $this->detector->getCurrentVersion();

        $this->assertEquals('2.2.2', $version->getVersion());
    }

    public function testFinishReleaseException()
    {
        system("git flow init -fd");

        $this->setExpectedException("\Liip\RMT\Exception", "Expected to find");
        $this->detector->getCurrentVersion();
    }

}
