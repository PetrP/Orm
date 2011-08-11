<?php

use Orm\ServiceContainer;

/**
 * @covers Orm\ServiceContainer::removeService
 */
class ServiceContainer_removeService_Test extends TestCase
{
	private $c;

	protected function setUp()
	{
		$this->c = new ServiceContainer;
	}

	public function test1()
	{
		$this->assertSame(false, $this->c->hasService('test'));
		$this->c->addService('test', NULL);
		$this->assertSame(true, $this->c->hasService('test'));
		$this->c->removeService('test');
		$this->assertSame(false, $this->c->hasService('test'));
		$this->c->addService('test', NULL);
		$this->assertSame(true, $this->c->hasService('test'));
		$this->c->removeService('test');
		$this->assertSame(false, $this->c->hasService('test'));
	}

	public function test2()
	{
		$this->c->addService('test', 'foo');
		$this->assertArrayHasKey('test', $this->readAttribute($this->c, 'services'));
		$this->c->removeService('test');
		$this->assertArrayNotHasKey('test', $this->readAttribute($this->c, 'services'));
	}

	public function testNotExists()
	{
		$this->setExpectedException('Orm\ServiceNotFoundException', "Service 'test' not found");
		$this->c->removeService('test');
	}

	public function testReturns()
	{
		$this->c->addService('test', 'foo');
		$this->assertSame($this->c, $this->c->removeService('test'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ServiceContainer', 'removeService');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
