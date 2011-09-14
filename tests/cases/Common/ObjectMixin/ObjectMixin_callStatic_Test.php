<?php

use Orm\ObjectMixin;

/**
 * @covers Orm\ObjectMixin::callStatic
 */
class ObjectMixin_callStatic_Test extends TestCase
{
	public function test()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Call to undefined static method Foo::foo().');
		ObjectMixin::callStatic('Foo', 'foo', array());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ObjectMixin', 'callStatic');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
