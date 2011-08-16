<?php

use Orm\ServiceNotFoundException;

/**
 * @covers Orm\ServiceNotFoundException
 */
class ServiceNotFoundException_Test extends TestCase
{

	public function test()
	{
		$e = new ServiceNotFoundException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\ServiceNotFoundException', $e);
	}

}
