<?php
namespace Liip\RMT\Tests\Unit\Changelog;

use Liip\RMT\Action\VersionStampAction;

class VersionStampActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * 
     * @var \Liip\RMT\Action\VersionStampAction 
     */
    private $action;
    
    protected function setUp()
    {
        parent::setUp();
        $this->action = new VersionStampAction(array(
            'file' => sys_get_temp_dir() . '/version.php',
            'const' => 'TEST_VERSION'
        ));
        
        $context = new \Liip\RMT\Context();
        $context->setParameter('new-version', 'XYZ');
        $this->action->setContext($context);
    }

    /**
     * Ensures that the version file is written
     */
    public function testWritesVersionFile()
    {
        $this->action->execute();
        $this->assertFileExists(sys_get_temp_dir() . '/version.php');
        $contents = file_get_contents(sys_get_temp_dir() . '/version.php');
        $this->assertContains('TEST_VERSION', $contents);
        $this->assertContains('XYZ', $contents);
    }
}
