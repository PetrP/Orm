<?php

use Orm\ServiceNotInstanceOfException;

/**
 * @covers Orm\ServiceNotInstanceOfException
 */
class ServiceNotInstanceOfException_Test extends TestCase
{

	public function test()
	{
		$e = new ServiceNotInstanceOfException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\ServiceNotInstanceOfException', $e);
	}

}
