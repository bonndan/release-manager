<?php
namespace Liip\RMT\Tests\Unit;

use Exception;
use Liip\RMT\Action\VcsCommitAction;
use Liip\RMT\Action\VcsTagAction;
use Liip\RMT\Application;
use Liip\RMT\Command\FinishCommand;
use Liip\RMT\Context;
use PHPUnit_Framework_TestCase;


/**
 * Description of FinishCommandTest
 *
 * @author daniel
 */
class FinishCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var FinishCommand
     */
    private $command;
    
    /**
     * @var Application
     */
    private $application;
    
    /**
     * @var Context
     */
    private $context;
    
    public function setUp()
    {
        $this->application = new Application();
        $this->context = Context::create($this->application);
        $this->context->setService('information-collector', $this->getMock("\Liip\RMT\Information\InformationCollector"));
        $this->command = new FinishCommand('finish', $this->application);
        $this->command->setContext($this->context);
    }
    
    public function testAutomaticallyAddsVcsCommitAction()
    {
        $this->runCommand();
        
        $postList = $this->context->getList(Context::POSTRELEASE_LIST);
        $this->assertNotEmpty($postList);
        foreach ($postList as $action) {
            if ($action instanceof VcsCommitAction) {
                return;
            }
        }
        
        $this->fail("No VCS commit action added to pst release list.");
    }
    
    
    protected function runCommand()
    {
        $input = $this->getMock("\Symfony\Component\Console\Input\InputInterface");
        $output = $this->getMock("\Liip\RMT\Output\Output");
        try {
            $this->command->run($input, $output);
        } catch (Exception $ex) {

        }
    }
}
