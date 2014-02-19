<?php
namespace Liip\RMT\Tests\Unit\Changelog;


class GitFlowFinishReleaseActionTest extends \PHPUnit_Framework_TestCase
{
    private $context;
    private $git;
    private $informationCollector;
    
    /**
     * system under test
     * @var \Liip\RMT\Action\GitFlowFinishReleaseAction 
     */
    private $action;
    
    public function setUp()
    {
        $this->git = $this->getMock("\Liip\RMT\VCS\GitFlow");
        
        $this->context = new \Liip\RMT\Context();
        $this->context->setService('vcs', $this->git);
        $this->context->setService('output', $this->getMock("\Liip\RMT\Output\Output"));
        $this->informationCollector = $this->getMock("\Liip\RMT\Information\InformationCollector");
        $this->context->setService('information-collector', $this->informationCollector);
        $this->action = new \Liip\RMT\Action\GitFlowFinishReleaseAction();
        $this->action->setContext($this->context);
    }
    
    public function testFinishRelease()
    {
        $comment = 'test';
        $this->informationCollector->expects($this->once())
                ->method('getValueFor')
                ->will($this->returnValue($comment));
        
        $this->git->expects($this->once())
                ->method('finishRelease')
                ->with($comment)
                ->will($this->returnValue('release/1.2.3'));
        
        $this->action->execute();
    }
    
    public function testFinishReleaseWorksWithDefaultGit()
    {
        $this->git = $this->getMock("\Liip\RMT\VCS\Git");
        $this->context->setService('vcs', $this->git);
        
        $comment = 'test';
        $this->informationCollector->expects($this->once())
                ->method('getValueFor')
                ->will($this->returnValue($comment));

        $this->setExpectedException("\Liip\RMT\Exception", "Expected to find");
        $this->action->execute();
    }
    
    public function testFinishReleaseException()
    {
        $comment = 'test';
        $this->informationCollector->expects($this->once())
                ->method('getValueFor')
                ->will($this->returnValue($comment));
        
        $this->git->expects($this->once())
                ->method('finishRelease')
                ->with($comment)
                ->will($this->throwException(new \Liip\RMT\Exception('test')));
        
        $this->setExpectedException("\Liip\RMT\Exception");
        $this->action->execute();
    }
}
