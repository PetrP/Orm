<?php

use Orm\NotValidException;

/**
 * @covers Orm\NotValidException
 */
class NotValidException_Test extends TestCase
{

	public function test()
	{
		$e = new NotValidException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\NotValidException', $e);
	}

	public function testMessage()
	{
		$e = new NotValidException('foo, bar');
		$this->assertSame('foo, bar', $e->getMessage());
	}

	public function testMessageArray()
	{
		$e = new NotValidException(array($this, 'foo', "'abc'", 'aaa'));
		$this->assertSame("Param NotValidException_Test::\$foo must be 'abc'; 'aaa' given.", $e->getMessage());

		$e = new NotValidException(array('NotValidException_Test', 'foo', "'abc'", $this));
		$this->assertSame("Param NotValidException_Test::\$foo must be 'abc'; 'NotValidException_Test' given.", $e->getMessage());

		$e = new NotValidException(array($this, 'foo', "'abc'", 123));
		$this->assertSame("Param NotValidException_Test::\$foo must be 'abc'; '123' given.", $e->getMessage());

		$e = new NotValidException(array($this, 'foo', "'abc'", NULL));
		$this->assertSame("Param NotValidException_Test::\$foo must be 'abc'; 'NULL' given.", $e->getMessage());

		$e = new NotValidException(array($this, 'foo', "'abc'", array()));
		$this->assertSame("Param NotValidException_Test::\$foo must be 'abc'; 'array' given.", $e->getMessage());

		$e = new NotValidException(array($this, 'foo', "'abc'", true));
		$this->assertSame("Param NotValidException_Test::\$foo must be 'abc'; 'boolean' given.", $e->getMessage());
	}
}
