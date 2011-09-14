<?php

use Orm\ObjectMixin;

/**
 * @covers Orm\ObjectMixin::call
 */
class ObjectMixin_call_Test extends TestCase
{
	public function test()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Call to undefined method ObjectMixin_call_Test::foo().');
		ObjectMixin::call($this, 'foo', array());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ObjectMixin', 'call');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
