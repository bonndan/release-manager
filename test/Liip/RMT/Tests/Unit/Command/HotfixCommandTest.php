<?php
namespace Liip\RMT\Tests\Unit\Command;

use Liip\RMT\Action\VcsTagAction;
use Liip\RMT\Command\HotfixCommand;
use Liip\RMT\Context;


/**
 * Description of FinishCommandTest
 *
 * @author daniel
 */
class HotfixCommandTest extends CommandTestCase
{
    /**
     * system under test
     * @var HotfixCommand
     */
    protected $command;
    
    
    public function setUp()
    {
        parent::setUp();
        $this->command = new HotfixCommand('hotfix', $this->application);
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
