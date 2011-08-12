<?php

/**
 * @covers Orm\ValueEntityFragment::onCreate
 */
class ValueEntityFragment_onCreate_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new TestEntity;
	}

	public function test()
	{
		$this->assertTrue($this->e->isChanged());
		$this->assertInternalType('array', $this->readAttribute($this->e, 'rules'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'onCreate');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
