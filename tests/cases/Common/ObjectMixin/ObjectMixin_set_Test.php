<?php

use Orm\ObjectMixin;

/**
 * @covers Orm\ObjectMixin::set
 */
class ObjectMixin_set_Test extends TestCase
{
	public function getBar()
	{
		return 3;
	}

	public function isBool()
	{
		return 4;
	}

	public function setSetter($v)
	{
		throw new Exception($v);
	}

	public function setSetter2($v)
	{
		return $v;
	}

	public function testEmpty()
	{
		$this->setExpectedException('Orm\MemberAccessException', "Cannot write to a class 'ObjectMixin_set_Test' property without name.");
		ObjectMixin::set($this, '', '');
	}

	public function test()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to an undeclared property ObjectMixin_set_Test::$foo.');
		ObjectMixin::set($this, 'foo', '');
	}

	public function testReadOnly()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to a read-only property ObjectMixin_set_Test::$bar.');
		ObjectMixin::set($this, 'bar', '');
	}

	public function testReadOnlyBool()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to a read-only property ObjectMixin_set_Test::$bool.');
		ObjectMixin::set($this, 'bool', '');
	}

	public function testSetter()
	{
		$this->setExpectedException('Exception', '456');
		ObjectMixin::set($this, 'setter', '456');
	}

	public function testSetter2()
	{
		$this->assertSame(NULL, ObjectMixin::set($this, 'setter2', '456'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ObjectMixin', 'set');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}
