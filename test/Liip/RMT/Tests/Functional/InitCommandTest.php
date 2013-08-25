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
        $helper = new \Liip\RMT\Helpers\ComposerConfig();
        $helper->setComposerFile($this->tempDir .'/composer.json');
        $this->assertNull($helper->getRMTConfigSection());
        
        exec('./RMT init --vcs=git --persister=vcs-tag -n', $output, $returnVar);
        $this->assertEquals(0, $returnVar);
        $config = $helper->getRMTConfigSection();
        
        $this->assertNotNull($config);
        $this->assertEquals('git', $config->getVcs());
        $this->assertEquals('vcs-tag', $config->getVersionPersister());
    }
}

