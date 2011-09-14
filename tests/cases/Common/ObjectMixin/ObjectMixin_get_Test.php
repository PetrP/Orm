<?php

use Orm\ObjectMixin;

/**
 * @covers Orm\ObjectMixin::get
 */
class ObjectMixin_get_Test extends TestCase
{
	public function getBar()
	{
		return 3;
	}

	public function isBool()
	{
		return 4;
	}

	public function testEmpty()
	{
		$this->setExpectedException('Orm\MemberAccessException', "Cannot read a class 'ObjectMixin_get_Test' property without name.");
		ObjectMixin::get($this, '');
	}

	public function test()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property ObjectMixin_get_Test::$foo.');
		ObjectMixin::get($this, 'foo');
	}

	public function testGetter()
	{
		$this->assertSame(3, ObjectMixin::get($this, 'bar'));
	}

	public function testBoolGetter()
	{
		$this->assertSame(4, ObjectMixin::get($this, 'bool'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ObjectMixin', 'get');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
