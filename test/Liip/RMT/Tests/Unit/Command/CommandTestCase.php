<?php
namespace Liip\RMT\Tests\Unit\Command;

use Liip\RMT\Application;
use Liip\RMT\Context;
use PHPUnit_Framework_TestCase;


/**
 * CommandTestCase
 *
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class CommandTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $application;
    
    /**
     * @var Context
     */
    protected $context;
    
    /**
     * @var \Liip\RMT\Information\InformationCollector 
     */
    protected $informationCollector;
    
    public function setUp()
    {
        $this->application = new Application();
        $this->context = Context::create($this->application);
        $this->informationCollector = $this->getMock("\Liip\RMT\Information\InformationCollector");
        $this->context->setService('information-collector', $this->informationCollector);
    }
    
    protected function runCommand()
    {
        $input = $this->getMock("\Symfony\Component\Console\Input\InputInterface");
        $input->expects($this->any())
                ->method('getOptions')
                ->will($this->returnValue(array()));
        $output = $this->getMock("\Liip\RMT\Output\Output");
        try {
            $this->command->run($input, $output);
        } catch (\Exception $ex) {
            if (strpos($ex->getMessage(), 'local modifications') !== false) {
                return;
            } else {
                throw $ex;
            }
        }
    }
}
