<?php
namespace Liip\RMT\Tests\Unit\Changelog;

use Liip\RMT\Action\GitFlowFinishAction;
use Liip\RMT\Context;
use Liip\RMT\Exception;
use Liip\RMT\Version\Detector\GitFlowBranch;
use PHPUnit_Framework_TestCase;


class GitFlowFinishActionTest extends PHPUnit_Framework_TestCase
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
        $this->prepareAction(GitFlowBranch::RELEASE);
    }
    
    private function prepareAction($branchType)
    {
        $this->git = $this->getMock("\Liip\RMT\VCS\GitFlow");
        
        $this->context = new Context();
        $this->context->setService('vcs', $this->git);
        $this->context->setService('output', $this->getMock("\Liip\RMT\Output\Output"));
        $this->informationCollector = $this->getMock("\Liip\RMT\Information\InformationCollector");
        $this->context->setService('information-collector', $this->informationCollector);
        $this->action = new GitFlowFinishAction($branchType);
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
    
    public function testFinishHotfix()
    {
        $this->prepareAction(GitFlowBranch::HOTFIX);
        $comment = 'test';
        $this->informationCollector->expects($this->once())
                ->method('getValueFor')
                ->will($this->returnValue($comment));
        
        $this->git->expects($this->once())
                ->method('finishHotfix')
                ->with($comment)
                ->will($this->returnValue('hotfix/1.2.3'));
        
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
                ->will($this->throwException(new Exception('test')));
        
        $this->setExpectedException("\Liip\RMT\Exception");
        $this->action->execute();
    }
}
