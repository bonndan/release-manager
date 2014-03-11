<?php
namespace Liip\RMT\Tests\Unit;


/**
 * Description of FinishCommandTest
 *
 * @author daniel
 */
class FinishCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var \Liip\RMT\Command\FinishCommand
     */
    private $command;
    
    /**
     * @var \Liip\RMT\Application
     */
    private $application;
    
    /**
     * @var \Liip\RMT\Context
     */
    private $context;
    
    public function setUp()
    {
        $this->application = new \Liip\RMT\Application();
        $this->context = \Liip\RMT\Context::create($this->application);
        $this->context->setService('information-collector', $this->getMock("\Liip\RMT\Information\InformationCollector"));
        $this->command = new \Liip\RMT\Command\FinishCommand('finish', $this->application);
        $this->command->setContext($this->context);
    }
    
    public function testAutomaticallyAddsVcsCommitAction()
    {
        $input = $this->getMock("\Symfony\Component\Console\Input\InputInterface");
        $output = $this->getMock("\Liip\RMT\Output\Output");
        try {
            $this->command->run($input, $output);
        } catch (\Exception $ex) {

        }
        
        $postList = $this->context->getList(\Liip\RMT\Context::POSTRELEASE_LIST);
        $this->assertNotEmpty($postList);
        $action = $postList->pop();
        $this->assertInstanceOf("\Liip\RMT\Action\VcsCommitAction", $action);
    }
}
