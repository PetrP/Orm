<?php

use Orm\NotCallableException;

/**
 * @covers Orm\NotCallableException
 */
class NotCallableException_Test extends TestCase
{

	public function test()
	{
		$e = new NotCallableException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\NotCallableException', $e);
	}

}
