<?php

namespace Liip\RMT\Tests\Unit\Helpers;

class ServiceBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * @var \Liip\RMT\Helpers\ServiceBuilder 
     */
    private $builder;
    
    public function setUp()
    {
        $context = new \Liip\RMT\Context();
        $this->builder = new \Liip\RMT\Helpers\ServiceBuilder($context);
    }
    
    /**
     * @dataProvider getDataForTestingGetClassAndOptions
     */
    public function testGetService($configKey, $rawConfig, $expectedClass, $expectedOptions)
    {
        $service = $this->builder->getService($rawConfig, $configKey);
        
        $this->assertInstanceOf($expectedClass, $service);
        if (!empty($expectedOptions)) {
            $this->assertAttributeEquals($expectedOptions, 'options', $service);
        }
    }

    public function getDataForTestingGetClassAndOptions()
    {
        return array(
            array('vcs', 'git', 'Liip\RMT\VCS\Git', array()),
            array('versionPersister', 'vcs-tag', 'Liip\RMT\Version\Persister\VcsTagPersister', array()),
            array('vcs', array('name' => 'git'), 'Liip\RMT\VCS\Git', array()),
            array('vcs', array('name' => 'git', 'opt1' => 'val1'), 'Liip\RMT\VCS\Git', array('opt1' => 'val1')),
            array('prerequisites_1', 'display-last-changes', 'Liip\RMT\Prerequisite\DisplayLastChanges', array())
        );
    }

}