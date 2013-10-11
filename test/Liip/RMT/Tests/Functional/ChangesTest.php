<?php

namespace Liip\RMT\Tests\Functional;

class ChangesTest extends RMTFunctionalTestBase
{

    public function testDisplayLastChange()
    {
        $this->createJsonConfig('semantic', 'vcs-tag', array(
            'prerequisites' => array(),
            'vcs' => 'git'
        ));
        $this->initGit();
        exec('git tag 0.0.1');
        exec('echo "foo" > fileFoo');
        exec('git add fileFoo');
        exec('git commit -m "Add a simple file"');
        exec('git mv fileFoo fileBar');
        exec('git commit -m "Rename foo to bar"');

        exec('./RMT changes', $consoleOutput, $exitCode);
        $consoleOutput = implode("\n", $consoleOutput);
        $this->assertNotContains("First commit", $consoleOutput);
        $this->assertContains("Add a simple file", $consoleOutput);
        $this->assertContains("Rename foo to bar", $consoleOutput);
    }
}