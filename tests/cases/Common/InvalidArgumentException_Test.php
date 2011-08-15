<?php

use Orm\InvalidArgumentException;

/**
 * @covers Orm\InvalidArgumentException
 */
class InvalidArgumentException_Test extends TestCase
{

	public function test()
	{
		$e = new InvalidArgumentException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\InvalidArgumentException', $e);
	}

	public function testMessage()
	{
		$e = new InvalidArgumentException('foo, bar');
		$this->assertSame('foo, bar', $e->getMessage());
	}

	public function testMessageArray()
	{
		$e = new InvalidArgumentException(array($this, 'foo()', 'abc', 'aaa'));
		$this->assertSame("InvalidArgumentException_Test::foo() must be abc; 'aaa' given.", $e->getMessage());

		$e = new InvalidArgumentException(array('InvalidArgumentException_Test', 'foo()', 'abc', $this));
		$this->assertSame("InvalidArgumentException_Test::foo() must be abc; 'InvalidArgumentException_Test' given.", $e->getMessage());

		$e = new InvalidArgumentException(array('InvalidArgumentException_Test', NULL, 'abc'));
		$this->assertSame("InvalidArgumentException_Test must be abc; 'NULL' given.", $e->getMessage());

		$e = new InvalidArgumentException(array(NULL, 'asd', 'abc'));
		$this->assertSame("asd must be abc; 'NULL' given.", $e->getMessage());

		$e = new InvalidArgumentException(array('aaa', 'bbb', 'abc', NULL, '; foo bar given'));
		$this->assertSame("aaa::bbb must be abc; foo bar given.", $e->getMessage());

		$e = new InvalidArgumentException(array('aaa', 'bbb', 'abc', 'xxx', '; foo bar given'));
		$this->assertSame("aaa::bbb must be abc; foo bar given.", $e->getMessage());
	}
}
