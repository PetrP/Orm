<?php

use Orm\ObjectMixin;

/**
 * @covers Orm\ObjectMixin::has
 */
class ObjectMixin_has_Test extends TestCase
{
	public function getBar()
	{

	}

	public function isBool()
	{

	}

	public function test()
	{
		$this->assertSame(false, ObjectMixin::has($this, ''));
		$this->assertSame(false, ObjectMixin::has($this, 'foo'));
		$this->assertSame(true, ObjectMixin::has($this, 'bar'));
		$this->assertSame(true, ObjectMixin::has($this, 'bool'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ObjectMixin', 'has');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
