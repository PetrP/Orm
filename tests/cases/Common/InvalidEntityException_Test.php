<?php

use Orm\InvalidEntityException;

/**
 * @covers Orm\InvalidEntityException
 */
class InvalidEntityException_Test extends TestCase
{

	public function test()
	{
		$e = new InvalidEntityException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\InvalidEntityException', $e);
	}

}
