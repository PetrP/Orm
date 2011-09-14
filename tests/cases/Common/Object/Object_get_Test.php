<?php

/**
 * @covers Orm\Object::__get
 */
class Object_get_Test extends TestCase
{

	public function testEmpty()
	{
		$o = new Object_Object;
		$this->setExpectedException('Orm\MemberAccessException', "Cannot read a class 'Object_Object' property without name.");
		$o->__get('');
	}

	public function test()
	{
		$o = new Object_Object;
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property Object_Object::$foo.');
		$o->__get('foo');
	}

	public function testGetter()
	{
		$o = new Object_Object;
		$this->assertSame(3, $o->__get('bar'));
	}

	public function testBoolGetter()
	{
		$o = new Object_Object;
		$this->assertSame(4, $o->__get('bool'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Object', '__get');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
