<?php

namespace Liip\RMT\Tests\Functional;

class GitTest extends RMTFunctionalTestBase
{

    public function testInitialVersionSemantic()
    {
        $this->initGit();
        $this->createJsonConfig('semantic', 'vcs-tag', array('vcs' => 'git'));
        exec('./RMT release -n  --type=patch --confirm-first');
        exec('git tag', $tags);
        $this->assertEquals(array('0.0.1'), $tags);
    }

    public function testSemantic()
    {
        $this->initGit();
        exec('git tag 2.1.19');
        $this->createJsonConfig('semantic', 'vcs-tag', array('vcs' => 'git'));
        exec('./RMT release -n --type=minor');
        exec('git tag', $tags);
//        $this->manualDebug();
        $this->assertEquals(array('2.1.19', '2.2.0'), $tags);
    }

    protected function initGit()
    {
        exec('git init');
        exec('git add *');
        exec('git commit -m "First commit"');
    }

}
