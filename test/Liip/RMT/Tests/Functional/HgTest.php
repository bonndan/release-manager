<?php

namespace Liip\RMT\Tests\Functional;

class HgTest extends RMTFunctionalTestBase
{

    public static function cleanTags($tags)
    {
        return array_map(function ($t) {
                $parts = explode(' ', $t);
                return $parts[0];
            }, $tags);
    }

    public function testInitialVersionSemantic()
    {
        $this->initHg();
        $this->createJsonConfig('semantic', 'vcs-tag', array('vcs' => 'hg'));
        exec('./RMT release -n  --type=patch --confirm-first');
        exec('hg tags', $tags);
        $this->assertEquals(array('tip', '0.0.1'), static::cleanTags($tags));
    }

    public function testSemantic()
    {
        $this->initHg();
        exec('hg tag 2.1.19');
        $this->createJsonConfig('semantic', 'vcs-tag', array('vcs' => 'hg'));
        exec('./RMT release -n --type=minor');
        exec('hg tags', $tags);
        $this->assertEquals(array('tip', '2.2.0', '2.1.19'), static::cleanTags($tags));
    }


    protected function initHg()
    {
        exec('hg init');
        exec('echo "[ui]" > .hg/hgrc');
        exec('echo "username = John Doe <test@test.com>" >> .hg/hgrc');
        exec('hg add *');
        exec('hg commit -m "First commit"');
    }

}
