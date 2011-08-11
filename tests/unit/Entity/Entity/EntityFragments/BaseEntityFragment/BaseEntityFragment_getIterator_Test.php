<?php

/**
 * @covers Orm\BaseEntityFragment::getIterator
 */
class BaseEntityFragment_getIterator_Test extends TestCase
{
	private $e;
	protected function setUp()
	{
		$this->e = new TestEntity;
	}

	public function test()
	{
		$this->assertInstanceOf('ArrayIterator', $this->e->getIterator());
	}

	public function test2()
	{
		$this->assertSame($this->e->toArray(), iterator_to_array($this->e));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseEntityFragment', 'getIterator');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
