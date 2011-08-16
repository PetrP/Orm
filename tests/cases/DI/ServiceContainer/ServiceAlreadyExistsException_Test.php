<?php

use Orm\ServiceAlreadyExistsException;

/**
 * @covers Orm\ServiceAlreadyExistsException
 */
class ServiceAlreadyExistsException_Test extends TestCase
{

	public function test()
	{
		$e = new ServiceAlreadyExistsException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\ServiceAlreadyExistsException', $e);
	}

}
