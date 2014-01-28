<?php

namespace Liip\RMT\Tests\Unit;

use Liip\RMT\Context;

use Liip\RMT\Tests\Unit\ServiceClass;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * system under test
     * 
     * @var \Liip\RMT\Context
     */
    private $context;
    
    public function setUp()
    {
        parent::setUp();
        $this->context = new Context();
    }

    public function _testSetAndGetService()
    {
        $this->context->setService('foo', '\Liip\RMT\Tests\Unit\ServiceClass');
        $objectFoo = $this->context->getService('foo');
        $this->assertInstanceOf('\Liip\RMT\Tests\Unit\ServiceClass', $objectFoo);
        $this->assertEquals($objectFoo, $this->context->getService('foo'), 'Two successive calls return the same object');
    }

    public function _testSetAndGetServiceWithObject()
    {
        $object = new ServiceClass();
        $this->context->setService('foo', $object);
        $this->assertEquals($object, $this->context->getService('foo'));
    }

    public function _testSetAndGetServiceWithOptions()
    {
        $options = array('pi'=>3.14);
        $this->context->setService('foo', '\Liip\RMT\Tests\Unit\ServiceClass', $options);
        $this->assertEquals($options, $this->context->getService('foo')->getOptions());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage There is no service defined with id [abc]
     */
    public function _testGetServiceWithoutSet()
    {
        $this->context->getService('abc');
    }

    /**
     * 
     */
    public function testSetServiceWithInvalidClass()
    {
        $this->setExpectedException("\Liip\RMT\Config\Exception");
        $this->context->setService('foo', 'Bar');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage setService() only accept an object or a valid class name
     */
    public function testSetServiceWithInvalidObject()
    {
         $this->context->setService('foo', 12);
    }

    // PARAM TESTS

    public function testSetAndGetParam()
    {
        $this->context->setParameter('date', '11.11.11');
        $this->assertEquals('11.11.11', $this->context->getParameter('date'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage There is no param defined with id [abc]
     */
    public function testGetParamWithoutSet()
    {
        $this->context->getParameter('abc');
    }


    // LIST TESTS

    public function testAddToList()
    {
        $this->context->addToList('prerequisites', new \Liip\RMT\Action\ComposerUpdateAction());
        $this->context->addToList('prerequisites', new \Liip\RMT\Action\ComposerUpdateAction());
        $objects = $this->context->getList('prerequisites');
        $this->assertCount(2, $objects);
        $this->assertInstanceOf('\Liip\RMT\Action\ComposerUpdateAction', $objects[0]);
        $this->assertInstanceOf('\Liip\RMT\Action\ComposerUpdateAction', $objects[1]);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage There is no list defined with id [abc]
     */
    public function testGetListParamWithoutAdd()
    {
        $this->context->getList('abc');
    }
    
    public function testNewVersion()
    {
        $version = new \Liip\RMT\Version('0.1.1');
        $this->context->setNewVersion($version);
        $this->assertSame($version, $this->context->getNewVersion());
    }
    
    public function testGetVersionDetector()
    {
        $application = new \Liip\RMT\Application();
        $context = Context::create($application);
        $actual = $context->getVersionDetector();
        $this->assertInstanceOf("\Liip\RMT\Version\Detector\DetectorInterface", $actual);
    }
}
