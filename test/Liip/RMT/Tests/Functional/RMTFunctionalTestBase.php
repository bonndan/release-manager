<?php

namespace Liip\RMT\Tests\Functional;

class RMTFunctionalTestBase extends \PHPUnit_Framework_TestCase
{

    protected $tempDir;

    /**
     * Test setup: add composer file and append config.
     */
    protected function setUp()
    {

        // Create a temp folder
        $this->tempDir = tempnam(sys_get_temp_dir(), '');
        if (file_exists($this->tempDir)) {
            unlink($this->tempDir);
        }
        mkdir($this->tempDir);
        chdir($this->tempDir);

        copy(__DIR__ . '/composer_no_rmt.json', $this->tempDir .'/composer.json');
        
        // Create the executable task inside
        $rmtDir = realpath(__DIR__ . '/../../../../../');
        exec("php $rmtDir/command.php init --persister=vcs-tag --vcs=git");
    }

    /**
     * 
     * @param type $generator
     * @param type $persister
     * @param type $otherConfig
     */
    protected function createJsonConfig($generator, $persister, $otherConfig = array())
    {
        $helper = new \Liip\RMT\Helpers\ComposerConfig();
        $helper->setComposerFile($this->tempDir . '/composer.json');
        $allConfig = array_merge($otherConfig, array(
            'versionPersister' => $persister,
        ));
        $config = \Liip\RMT\Config::create($allConfig);
        $helper->addRMTConfigSection($config);
        
        return $helper->getRMTConfigSection();
    }

    protected function tearDown()
    {
        if (!$this->hasFailed()) {
            exec('rm -rf ' . $this->tempDir);
        }
    }

    protected function initGit()
    {
        exec('git init');
        exec('git add *');
        exec('git commit -m "First commit"');
    }

    protected function manualDebug($output = '')
    {
        echo "\n\nMANUAL DEBUG Go to:\n > cd " . $this->tempDir . "\n\n";
        echo $output;
        exit();
    }

}
