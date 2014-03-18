<?php
namespace Liip\RMT\Tests\Unit;


class VcsCommitActionTest extends \PHPUnit_Framework_TestCase
{
    public function testFailsGracefully()
    {
        $context = $this->getMock("\Liip\RMT\Context");
        $action = new \Liip\RMT\Action\VcsCommitAction();
        $action->setContext($context);
        $action->setFailsGracefully(true);
        
        $context->expects($this->once())
                ->method('getVCS')
                ->will($this->throwException(new \Liip\RMT\Exception('test')));
        
        $output = $this->getMock("\Liip\RMT\Output\Output");
        $context->expects($this->once())
                ->method('getOutput')
                ->will($this->returnValue($output));
        
        $this->setExpectedException(null);
        $action->execute();
    }
    
}
