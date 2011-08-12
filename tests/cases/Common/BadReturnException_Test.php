<?php

use Orm\BadReturnException;

/**
 * @covers Orm\BadReturnException
 */
class BadReturnException_Test extends TestCase
{

	public function test()
	{
		$e = new BadReturnException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\BadReturnException', $e);
	}

	public function testMessage()
	{
		$e = new BadReturnException('foo, bar');
		$this->assertSame('foo, bar', $e->getMessage());
	}

	public function testMessageArray()
	{
		$e = new BadReturnException(array($this, 'foo', 'abc', 'aaa'));
		$this->assertSame("BadReturnException_Test::foo() must return abc, 'string' given.", $e->getMessage());

		$e = new BadReturnException(array('BadReturnException_Test', 'foo', 'abc', $this));
		$this->assertSame("BadReturnException_Test::foo() must return abc, 'BadReturnException_Test' given.", $e->getMessage());

		$e = new BadReturnException(array('BadReturnException_Test', NULL, 'abc'));
		$this->assertSame("BadReturnException_Test must return abc, 'NULL' given.", $e->getMessage());

		$e = new BadReturnException(array(NULL, 'asd', 'abc'));
		$this->assertSame("asd must return abc, 'NULL' given.", $e->getMessage());

		$e = new BadReturnException(array('aaa', 'bbb', 'abc', NULL, ', foo bar given'));
		$this->assertSame("aaa::bbb() must return abc, foo bar given.", $e->getMessage());

		$e = new BadReturnException(array('aaa', 'bbb', 'abc', 'xxx', ', foo bar given'));
		$this->assertSame("aaa::bbb() must return abc, foo bar given.", $e->getMessage());
	}
}
