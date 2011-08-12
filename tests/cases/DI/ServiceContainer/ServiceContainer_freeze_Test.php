<?php

use Orm\ServiceContainer;

/**
 * @covers Orm\ServiceContainer::freeze
 */
class ServiceContainer_freeze_Test extends TestCase
{
	private $c;

	protected function setUp()
	{
		$this->c = new ServiceContainer;
	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->freeze());
	}

	public function test()
	{
		$this->assertAttributeSame(false, 'frozen', $this->c);
		$this->c->freeze();
		$this->assertAttributeSame(true, 'frozen', $this->c);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ServiceContainer', 'freeze');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
