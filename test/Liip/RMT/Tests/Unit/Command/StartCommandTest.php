<?php
namespace Liip\RMT\Tests\Unit\Command;

use Liip\RMT\Action\VcsTagAction;
use Liip\RMT\Command\HotfixCommand;
use Liip\RMT\Command\StartCommand;
use Liip\RMT\Context;


/**
 * StartCommandTest
 *
 * @author daniel
 */
class StartCommandTest extends CommandTestCase
{
    /**
     * system under test
     * @var HotfixCommand
     */
    protected $command;
    
    
    public function setUp()
    {
        parent::setUp();
        $this->command = new StartCommand('start', $this->application);
        $this->command->setContext($this->context);
    }

    
    public function testChecksTagActionsInPreReleaseList()
    {
        $this->context->getList(Context::PRERELEASE_LIST)->push(new VcsTagAction());
        
        $this->setExpectedException("Liip\RMT\Config\Exception");
        $this->runCommand();
        
    }
    
    public function testChecksTagActionsInPostReleaseList()
    {
        $this->context->getList(Context::POSTRELEASE_LIST)->push(new VcsTagAction());
        
        $this->setExpectedException("Liip\RMT\Config\Exception");
        $this->runCommand();
        
    } 
   
}
