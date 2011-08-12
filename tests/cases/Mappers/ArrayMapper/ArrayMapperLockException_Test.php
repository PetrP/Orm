<?php

use Orm\ArrayMapperLockException;

/**
 * @covers Orm\ArrayMapperLockException
 */
class ArrayMapperLockException_Test extends TestCase
{

	public function test()
	{
		$e = new ArrayMapperLockException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\ArrayMapperLockException', $e);
	}

}
