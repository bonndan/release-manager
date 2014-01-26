<?php
namespace Liip\RMT\Tests;

/**
 * Description of ComposerPersisterTest
 *
 * @author daniel
 */
class ComposerPersisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var \Liip\RMT\Version\Persister\ComposerPersister 
     */
    private $persister;
    
    public function setUp()
    {
        $config = new \Liip\RMT\Helpers\ComposerConfig();
        $file = (__DIR__ . '/../../../../../../../composer.json');
        $config->setComposerFile($file);
        $this->persister = new \Liip\RMT\Version\Persister\ComposerPersister($config);
    }
    
    public function testGetCurrentVersion()
    {
        $version = $this->persister->getCurrentVersion();
        $this->assertInstanceOf("\Liip\RMT\Version", $version);
    }
}
