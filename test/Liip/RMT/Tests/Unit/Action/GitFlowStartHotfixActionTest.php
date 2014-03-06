<?php
namespace Liip\RMT\Tests\Unit\Changelog;


class GitFlowStartHotfixActionTest extends \PHPUnit_Framework_TestCase
{
    private $context;
    private $git;
    /**
     * system under test
     * @var \Liip\RMT\Action\GitFlowStartReleaseAction 
     */
    private $action;
    
    public function setUp()
    {
        $this->git = $this->getMock("\Liip\RMT\VCS\GitFlow");
        
        $this->context = new \Liip\RMT\Context();
        $this->context->setService('vcs', $this->git);
        $this->context->setService('output', $this->getMock("\Liip\RMT\Output\Output"));
        
        $this->action = new \Liip\RMT\Action\GitFlowStartHotfixAction();
        $this->action->setContext($this->context);
    }
    
    public function testStartHotfix()
    {
        $version = new \Liip\RMT\Version('1.2.3');
        $this->git->expects($this->once())
                ->method('startHotfix')
                ->with($version);
        $this->context->setParameter('new-version', $version);
        $this->action->execute();
    }
}
