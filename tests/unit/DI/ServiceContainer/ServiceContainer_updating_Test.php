<?php

use Orm\ServiceContainer;

/**
 * @covers Orm\ServiceContainer::updating
 */
class ServiceContainer_updating_Test extends TestCase
{
	private $c;

	protected function setUp()
	{
		$this->c = new ServiceContainer;
	}

	public function testRemoveService1()
	{
		$this->c->addService('test', NULL);
		$this->c->removeService('test', NULL);
		$this->assertTrue(true);
	}

	public function testRemoveService2()
	{
		$this->c->addService('test', NULL);
		$this->c->freeze();
		$this->setExpectedException('Orm\FrozenContainerException', 'Cannot modify a frozen container.');
		$this->c->removeService('test', NULL);
	}

	public function testAddService1()
	{
		$this->c->addService('test', NULL);
		$this->assertTrue(true);
	}

	public function testAddService2()
	{
		$this->c->freeze();
		$this->setExpectedException('Orm\FrozenContainerException', 'Cannot modify a frozen container.');
		$this->c->addService('test', NULL);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ServiceContainer', 'updating');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
