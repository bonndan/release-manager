<?php
namespace Liip\RMT\Tests\Unit\Command;

use Liip\RMT\Action\VcsCommitAction;
use Liip\RMT\Action\VcsTagAction;
use Liip\RMT\Command\FinishCommand;
use Liip\RMT\Context;


/**
 * Description of FinishCommandTest
 *
 * @author daniel
 */
class FinishCommandTest extends CommandTestCase
{
    /**
     * system under test
     * @var FinishCommand
     */
    protected $command;
    
    
    public function setUp()
    {
        parent::setUp();
        $this->command = new FinishCommand('finish', $this->application);
        $this->command->setContext($this->context);
    }
    
    public function testAutomaticallyAddsVcsCommitAction()
    {
        $this->context->setParameter('type', 'patch');
        $this->context->getInformationCollector()->setValueFor('type', 'patch');
        $this->runCommand();
        
        $postList = $this->context->getList(Context::POSTRELEASE_LIST);
        $this->assertNotEmpty($postList);
        foreach ($postList as $action) {
            if ($action instanceof VcsCommitAction) {
                return;
            }
        }
        
        $this->fail("No VCS commit action added to post release list.");
    }
    
}
