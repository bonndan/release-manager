<?php

namespace Liip\RMT\Tests\Functional;


class CurrentCommandTest extends RMTFunctionalTestBase
{
    public function testRaw()
    {
        $this->initGit();
        $this->createJsonConfig('semantic', 'vcs-tag', array('vcs'=>'git'));
        exec('git tag 2.3.4');
        $this->assertEquals("2.3.4", exec('./RMT current --raw'));
    }

    public function testVCSTag()
    {
        $this->initGit();
        $this->createJsonConfig('semantic', array('name'=>'vcs-tag'), array('vcs'=>'git'));
        exec('git tag 2.3.4');
        $this->assertEquals("2.3.4", exec('./RMT current --raw'));
    }

    public function testNumericCompare()
    {
        $this->initGit();
        $this->createJsonConfig('semantic', 'vcs-tag', array('vcs'=>'git'));
        exec('git tag 1.3.11');
        exec('git tag 1.3.10');
        exec('git tag 1.3.1');
        $this->assertEquals("1.3.11", exec('./RMT current --raw'));
    }

}

