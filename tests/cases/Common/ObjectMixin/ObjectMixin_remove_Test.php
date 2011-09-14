<?php

use Orm\ObjectMixin;

/**
 * @covers Orm\ObjectMixin::remove
 */
class ObjectMixin_remove_Test extends TestCase
{
	public function test()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot unset the property ObjectMixin_remove_Test::$foo.');
		ObjectMixin::remove($this, 'foo');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ObjectMixin', 'remove');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
