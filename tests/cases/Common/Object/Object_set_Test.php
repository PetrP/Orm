<?php

/**
 * @covers Orm\Object::__set
 */
class Object_set_Test extends TestCase
{

	public function testEmpty()
	{
		$o = new Object_Object;
		$this->setExpectedException('Orm\MemberAccessException', "Cannot write to a class 'Object_Object' property without name.");
		$o->__set('', '');
	}

	public function test()
	{
		$o = new Object_Object;
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to an undeclared property Object_Object::$foo.');
		$o->__set('foo', '');
	}

	public function testReadOnly()
	{
		$o = new Object_Object;
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to a read-only property Object_Object::$bar.');
		$o->__set('bar', '');
	}

	public function testReadOnlyBool()
	{
		$o = new Object_Object;
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to a read-only property Object_Object::$bool.');
		$o->__set('bool', '');
	}

	public function testSetter()
	{
		$o = new Object_Object;
		$this->setExpectedException('Exception', '456');
		$o->__set('setter', '456');
	}

	public function testSetter2()
	{
		$o = new Object_Object;
		$this->assertSame(NULL, $o->__set('setter2', '456'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Object', '__set');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
