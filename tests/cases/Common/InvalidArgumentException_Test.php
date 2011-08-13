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

}
