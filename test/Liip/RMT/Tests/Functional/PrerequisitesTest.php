<?php

namespace Liip\RMT\Tests\Functional;

class PrerequisitesTest extends RMTFunctionalTestBase
{

    public function testDisplayLastChange()
    {
        $this->createJsonConfig('semantic', 'vcs-tag', array(
            'prerequisites' => array('display-last-changes'),
            'vcs' => 'git'
        ));
        $this->initGit();
        exec('git tag 0.0.1');
        exec('echo "foo" > fileFoo');
        exec('git add fileFoo');
        exec('git commit -m "Add a simple file"');
        exec('git mv fileFoo fileBar');
        exec('git commit -m "Rename foo to bar"');

        exec('./RMT release -n', $consoleOutput, $exitCode);
        $consoleOutput = implode("\n", $consoleOutput);
        $this->assertNotContains("First commit", $consoleOutput);
        $this->assertContains("Add a simple file", $consoleOutput);
        $this->assertContains("Rename foo to bar", $consoleOutput);
    }

    public function testWorkingCopyCheckFailsWithLocalModifications()
    {
        $this->createJsonConfig('semantic', 'vcs-tag', array(
            'prerequisites' => array('working-copy-check'),
            'vcs' => 'git'
        ));
        $this->initGit();
        exec('git tag 1');

        // Release blocked by the check
        exec('touch toto');
        exec('./RMT release -n 2>&1', $consoleOutput, $exitCode);
        $this->assertGreaterThan(0, $exitCode);
    }

    /**
     * Ensures that a tag is created when checks are ignored
     */
    public function testWorkingCopyWithIgnoreCheck()
    {
        $this->createJsonConfig('semantic', 'vcs-tag', array(
            'prerequisites' => array('working-copy-check'),
            'vcs' => 'git'
        ));
        $this->initGit();
        exec('git tag 0.0.1');

        // Release working, check is ignore
        exec('./RMT release -n --ignore-check', $consoleOutput, $exitCode);
        $this->assertEquals(0, $exitCode);
        exec('git tag', $tags);
        $this->assertEquals(array('0.0.1', '0.0.2'), $tags);
    }

    /**
     * Ensures that a tag is created with a clean working dir
     */
    public function testWorkingCopy()
    {
        $this->createJsonConfig('semantic', 'vcs-tag', array(
            'prerequisites' => array('working-copy-check'),
            'vcs' => 'git'
        ));
        $this->initGit();
        exec('git tag 0.0.1');

        // Normal case, check is passing
        exec('./RMT release -n', $consoleOutput, $exitCode);
        $this->assertEquals(0, $exitCode, implode(PHP_EOL, $consoleOutput));
        exec('git tag', $tags2);
        $this->assertEquals(array('0.0.1', '0.0.2'), $tags2);
    }

}
