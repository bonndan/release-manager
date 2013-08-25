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

    public function testSetAndGetService()
    {
        $this->context->setService('foo', '\Liip\RMT\Tests\Unit\ServiceClass');
        $objectFoo = $this->context->getService('foo');
        $this->assertInstanceOf('\Liip\RMT\Tests\Unit\ServiceClass', $objectFoo);
        $this->assertEquals($objectFoo, $this->context->getService('foo'), 'Two successive calls return the same object');
    }

    public function testSetAndGetServiceWithObject()
    {
        $object = new ServiceClass();
        $this->context->setService('foo', $object);
        $this->assertEquals($object, $this->context->getService('foo'));
    }

    public function testSetAndGetServiceWithOptions()
    {
        $options = array('pi'=>3.14);
        $this->context->setService('foo', '\Liip\RMT\Tests\Unit\ServiceClass', $options);
        $this->assertEquals($options, $this->context->getService('foo')->getOptions());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage There is no service define with id [abc]
     */
    public function testGetServiceWithoutSet()
    {
        $this->context->getService('abc');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The class [Bar] does not exist
     */
    public function testSetServiceWithInvalidClass()
    {
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
     * @expectedExceptionMessage There is no param define with id [abc]
     */
    public function testGetParamWithoutSet()
    {
        $this->context->getParameter('abc');
    }


    // LIST TESTS

    public function testAddToList()
    {
        $this->context->addToList('prerequisites', '\Liip\RMT\Tests\Unit\ServiceClass');
        $this->context->addToList('prerequisites', '\Liip\RMT\Context');
        $objects = $this->context->getList('prerequisites');
        $this->assertCount(2, $objects);
        $this->assertInstanceOf('\Liip\RMT\Tests\Unit\ServiceClass', $objects[0]);
        $this->assertInstanceOf('\Liip\RMT\Context', $objects[1]);
    }

    public function testAddToListWithOptions()
    {
        $options = array('pi'=>3.14);
        $this->context->addToList('foo', '\Liip\RMT\Tests\Unit\ServiceClass', $options);
        $objects = $this->context->getList('foo');
        $this->assertEquals($options, $objects[0]->getOptions());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage There is no list define with id [abc]
     */
    public function testGetListParamWithoutAdd()
    {
        $this->context->getList('abc');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The class [Bar] does not exist
     */
    public function testAddToListWithInvalidClass()
    {
        $this->context->addToList('foo', 'Bar');
    }


    public function testEmptyList()
    {
        $this->context->createEmptyList('prerequisites');
        $this->assertEquals(array(), $this->context->getList('prerequisites'));
    }
}
