<?php

namespace Liip\RMT\Tests\Functional;


class InitCommandTest extends RMTFunctionalTestBase
{
    /**
     * Ensures that the composer file is extended with the rmt config data.
     * 
     */
    public function testInitConfig()
    {
        copy(__DIR__ . '/composer_no_rmt.json', $this->tempDir .'/composer.json');
        exec('./RMT init --vcs=git --persister=vcs-tag -n');
        
        $helper = new \Liip\RMT\Helpers\ComposerConfig();
        $helper->setComposerFile($this->tempDir .'/composer.json');
        $config = $helper->getRMTConfigSection();
        
        $this->assertNotNull($config);
        $this->assertEquals('git', $config['vcs']);
        $this->assertEquals('vcs-tag', $config['version-persister']);
    }
}

