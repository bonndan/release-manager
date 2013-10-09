<?php
namespace Liip\RMT\Tests\Unit\Changelog;


class  ExecuteActionTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteFails()
    {
        $script = "nonsense 2>&1";
        $action = $this->createAction($script);
        
        $this->setExpectedException("\RuntimeException");
        $action->execute();
    }
    
    public function testExecuteScriptIsSuccessful()
    {
        $target =  sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename(__FILE__);
        @unlink($target);
        $script = "cp " . __FILE__ . ' ' . $target;
        
        $action = $this->createAction($script);
        $action->execute();
        $this->assertFileExists($target);
        @unlink($target);
    }
    
    /**
     * Ensures the "script" option param is checked.
     */
    public function testChecksRequiredOption()
    {
        $this->setExpectedException("\LogicException");
        new \Liip\RMT\Action\ExecuteAction(array());
    }
    
    /**
     * 
     * @param string $script
     * @return \Liip\RMT\Action\ExecuteAction
     */
    protected function createAction($script)
    {
        $action = new \Liip\RMT\Action\ExecuteAction(array('script' => $script));
        
        $context = new \Liip\RMT\Context();
        $context->setService('output', $this->getMock("\Liip\RMT\Output\Output"));
        $action->setContext($context);
        return $action;
    }

}
