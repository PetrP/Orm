<?php

use Orm\ServiceContainer;

/**
 * @covers Orm\ServiceContainer::addService
 */
class ServiceContainer_addService_Test extends TestCase
{
	private $c;

	protected function setUp()
	{
		$this->c = new ServiceContainer;
	}

	public function testAlready()
	{
		$this->c->addService('test', NULL);
		$this->setExpectedException('Orm\ServiceAlreadyExistsException', "Service 'test' already exists");
		$this->c->addService('test', NULL);
	}

	public function test()
	{
		$this->c->addService('test', 'foo');
		$s = $this->readAttribute($this->c, 'services');
		$this->assertArrayHasKey('test', $s);
		$s = (array) $s['test'];
		$this->assertSame(2, count($s));
		$this->assertSame(NULL, $s['service']);
		$this->assertSame('foo', $s['factory']);
	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->addService('test', 'foo'));
	}

}
