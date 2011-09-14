<?php

/**
 * @covers Orm\Object::__call
 */
class Object_call_Test extends TestCase
{
	public function test()
	{
		$o = new Object_Object;
		$this->setExpectedException('Orm\MemberAccessException', 'Call to undefined method Object_Object::foo().');
		$o->__call('foo', array());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Object', '__call');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
