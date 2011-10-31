<?php

use Orm\RecursiveException;

/**
 * @covers Orm\RecursiveException
 */
class RecursiveException_Test extends TestCase
{

	public function test()
	{
		$e = new RecursiveException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\InvalidEntityException', $e); // bc
		$this->assertInstanceOf('Orm\RecursiveException', $e);
	}

}
