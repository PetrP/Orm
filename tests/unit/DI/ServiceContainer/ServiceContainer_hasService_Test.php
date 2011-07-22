<?php

use Orm\ServiceContainer;

/**
 * @covers Orm\ServiceContainer::hasService
 */
class ServiceContainer_hasService_Test extends TestCase
{
	private $c;

	protected function setUp()
	{
		$this->c = new ServiceContainer;
	}

	public function testNot()
	{
		$this->assertFalse($this->c->hasService('test'));
	}

	public function testHas()
	{
		$this->c->addService('test', NULL);
		$this->assertTrue($this->c->hasService('test'));
	}

	public function testRemove()
	{
		$this->c->addService('test', NULL);
		$this->assertTrue($this->c->hasService('test'));
		$this->c->removeService('test');
		$this->assertFalse($this->c->hasService('test'));
	}

	public function testThrowNot()
	{
		$this->setExpectedException('Orm\ServiceNotFoundException', "Service 'test' not found");
		$this->c->hasService('test', true);
	}

	public function testThrowHas()
	{
		$this->c->addService('test', NULL);
		$this->assertTrue($this->c->hasService('test', true));
	}

}
