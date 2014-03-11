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
        $context = \Liip\RMT\Context::create($this->application);
        $context->setService('information-collector', $this->getMock("\Liip\RMT\Information\InformationCollector"));
        $this->command = new \Liip\RMT\Command\FinishCommand('finish', $this->application);
        $this->command->setContext($context);
    }
    
    public function testAutomaticallyAddsVcsCommitAction()
    {
        $input = $this->getMock("\Symfony\Component\Console\Input\InputInterface");
        $output = $this->getMock("\Liip\RMT\Output\Output");
        $this->command->run($input, $output);
        
        $postList = $this->context->getList(\Liip\RMT\Context::POSTRELEASE_LIST);
        $this->assertNotEmpty($postList);
    }
}
