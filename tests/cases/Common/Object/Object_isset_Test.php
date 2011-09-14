<?php

/**
 * @covers Orm\Object::__isset
 */
class Object_isset_Test extends TestCase
{

	public function test()
	{
		$o = new Object_Object;
		$this->assertSame(false, $o->__isset(''));
		$this->assertSame(false, $o->__isset('foo'));
		$this->assertSame(true, $o->__isset('bar'));
		$this->assertSame(true, $o->__isset('bool'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Object', '__isset');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
