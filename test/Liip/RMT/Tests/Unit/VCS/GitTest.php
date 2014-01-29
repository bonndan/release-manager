<?php

namespace Liip\RMT\Tests\Unit\Version;

use Liip\RMT\VCS\Git;

class GitTest extends \PHPUnit_Framework_TestCase
{
    protected $testDir;

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
    }

    public function testGetAllModificationsSince()
    {
        $vcs = new Git();
        $modifs = $vcs->getAllModificationsSince('1.1.0');
        $this->assertContains("Add a third file", $modifs[0]);
        $this->assertContains("Modification of the first file", $modifs[1]);
    }

    public function testGetModifiedFilesSince()
    {
        $vcs = new Git();
        $files = $vcs->getModifiedFilesSince('1.1.0');
        $this->assertEquals(array("file1" => "M", "file3" => "A"), $files);
    }

    public function testGetLocalModifications()
    {
        $vcs = new Git();
        exec('touch foo');
        $modifs = $vcs->getLocalModifications();
        $this->assertContains('foo', implode($modifs));
    }

    public function testGetTags()
    {
        $vcs = new Git();
        $this->assertEquals(array("1.0.0","1.1.0"), $vcs->getTags());
    }
    
    public function testGetCurrentVersion()
    {
        $vcs = new Git();
        $this->assertEquals("1.1.0", $vcs->getCurrentVersion());
    }

    public function testCreateTag()
    {
        $vcs = new Git();
        $vcs->createTag(new \Liip\RMT\Version('2.0.0'));
        $this->assertEquals(array("1.0.0","1.1.0","2.0.0"), $vcs->getTags());
    }

    public function testSaveWorkingCopy()
    {
        $vcs = new Git();
        $vcs->createTag(new \Liip\RMT\Version('2.0.0'));
        $this->assertEquals(array(), $vcs->getAllModificationsSince('2.0.0'));
        exec('rm file2');
        $vcs->saveWorkingCopy('Remove the second file');
        $this->assertCount(1,$vcs->getAllModificationsSince('2.0.0'));
    }

    public function testGetCurrentBranch()
    {
        $vcs = new Git();
        $this->assertEquals('master', $vcs->getCurrentBranch());
        system("git checkout -b foo --quiet");
        $this->assertEquals('foo', $vcs->getCurrentBranch());
        exec("git checkout master --quiet");
        $this->assertEquals('master', $vcs->getCurrentBranch());
    }

    /**
     * @expectedException \Liip\RMT\Exception
     * @expectedExceptionMessage Not currently on any branch
     */

    public function testGetCurrentBranchWhenNotInBranch()
    {
        $vcs = new Git();
        exec("git checkout 9aca70b --quiet");
        $vcs->getCurrentBranch();
    }
    
    public function testStartRelease()
    {
        $version = new \Liip\RMT\Version('2.2.2');
        $vcs = new Git();
        $vcs->setDryRun(true);
        $cmd = $vcs->startRelease($version);
        $this->assertEquals('flow release start 2.2.2', $cmd);
    }
    
    public function testFinishRelease()
    {
        $vcs = new Git();
        
        system("git flow init -fd 1>/dev/null 2>&1");
        system("git flow release start 2.2.2 1>/dev/null 2>&1");
        $vcs->setDryRun(true);
        $cmd = $vcs->finishRelease('test');
        $this->assertEquals('flow release finish -F -m "test" 2.2.2', $cmd);
    }
    
    public function testFinishReleaseException()
    {
        $vcs = new Git();
        system("git flow init -fd 1>/dev/null 2>&1");
        
        $this->setExpectedException("\Liip\RMT\Exception", "Expected to find");
        $vcs->finishRelease('test');
    }

    protected function tearDown()
    {
        // Remove the test folder
        exec('rm -rf '.$this->testDir);
        chdir(__DIR__);
    }

}
