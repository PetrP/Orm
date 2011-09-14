<?php

use Orm\Object;

/**
 * @covers Orm\Object::__callStatic
 */
class Object_callStatic_Test extends TestCase
{
	public function test()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Call to undefined static method Orm\Object::foo().');
		Object::__callStatic('foo', array());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Object', '__callStatic');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
