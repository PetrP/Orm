<?php

/**
 * @covers Orm\ValueEntityFragment::__set
 * @see ValueEntityFragment_setter_Test
 */
class ValueEntityFragment_set_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new ValueEntityFragment_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to an undeclared property ValueEntityFragment_getset_Entity::$unexists.');
		$this->e->unexists = 3;
	}

	public function testNoMetaMethod()
	{
		$this->e->method = 3;
		$this->assertSame(3, $this->e->method);
		$this->e->method = 'aa';
		$this->assertSame('aa', $this->e->method);
	}

	public function testReadOnly()
	{
		$this->setExpectedException('Orm\PropertyAccessException', 'Cannot write to a read-only property ValueEntityFragment_getset_Entity::$readOnly.');
		$this->e->readOnly = 'xyz';
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ValueEntityFragment', '__set');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
