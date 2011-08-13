<?php

/**
 * @covers Orm\ValueEntityFragment::setValue
 * @see ValueEntityFragment_setter_Test
 */
class ValueEntityFragment_setValue_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new ValueEntityFragment_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\PropertyAccessException', 'Cannot write to an undeclared property ValueEntityFragment_getset_Entity::$unexists.');
		$this->e->sv('unexists', 123);
	}

	public function test()
	{
		$this->assertSame($this->e, $this->e->sv('string', 'xyz'));
		$this->assertSame('xyz', $this->e->string);
	}

	public function testReadOnly()
	{
		$this->setExpectedException('Orm\PropertyAccessException', 'Cannot write to a read-only property ValueEntityFragment_getset_Entity::$readOnly.');
		$this->e->sv('readOnly', 'xyz');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', 'setValue');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
