<?php
namespace Liip\RMT\Tests\Unit\Changelog;


class GitFlowStartReleaseActionTest extends \PHPUnit_Framework_TestCase
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
        $this->git = $this->getMock("\Liip\RMT\VCS\Git");
        
        $this->context = new \Liip\RMT\Context();
        $this->context->setService('vcs', $this->git);
        $this->context->setService('output', $this->getMock("\Liip\RMT\Output\Output"));
        $this->action = new \Liip\RMT\Action\GitFlowStartReleaseAction();
        $this->action->setContext($this->context);
    }
    
    public function testStartRelease()
    {
        $version = new \Liip\RMT\Version('1.2.3');
        $this->git->expects($this->once())
                ->method('startRelease')
                ->with($version);
        $this->context->setParameter('new-version', $version);
        $this->action->execute();
    }
}
