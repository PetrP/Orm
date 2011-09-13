<?php

use Orm\BadEntityException;

/**
 * @covers Orm\BadEntityException
 */
class BadEntityException_Test extends TestCase
{

	public function test()
	{
		$e = new BadEntityException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\BadEntityException', $e);
	}

}
