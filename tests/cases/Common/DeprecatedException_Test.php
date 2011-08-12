<?php

use Orm\DeprecatedException;

/**
 * @covers Orm\DeprecatedException
 */
class DeprecatedException_Test extends TestCase
{

	public function test()
	{
		$e = new DeprecatedException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\DeprecatedException', $e);
	}

	public function testMessage()
	{
		$e = new DeprecatedException('foo, bar');
		$this->assertSame('foo, bar', $e->getMessage());
	}

	public function testMessageArray()
	{
		$e = new DeprecatedException(array($this, 'foo()', new ArrayObject, 'bar()'));
		$this->assertSame('DeprecatedException_Test::foo() is deprecated; use ArrayObject::bar() instead.', $e->getMessage());

		$e = new DeprecatedException(array('abc', '$foo', 'bca', '$bar'));
		$this->assertSame('abc::$foo is deprecated; use bca::$bar instead.', $e->getMessage());

		$e = new DeprecatedException(array('abc', '$foo'));
		$this->assertSame('abc::$foo is deprecated.', $e->getMessage());

		$e = new DeprecatedException(array('abc', '$foo'));
		$this->assertSame('abc::$foo is deprecated.', $e->getMessage());

		$e = new DeprecatedException(array(NULL, '$foo', NULL, 'asd'));
		$this->assertSame('$foo is deprecated; use asd instead.', $e->getMessage());

		$e = new DeprecatedException(array('$foo', NULL, 'asd'));
		$this->assertSame('$foo is deprecated; use asd instead.', $e->getMessage());
	}
}
