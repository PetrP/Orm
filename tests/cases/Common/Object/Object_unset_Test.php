<?php

/**
 * @covers Orm\Object::__unset
 */
class Object_unset_Test extends TestCase
{
	public function test()
	{
		$o = new Object_Object;
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot unset the property Object_Object::$foo.');
		$o->__unset('foo');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Object', '__unset');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
