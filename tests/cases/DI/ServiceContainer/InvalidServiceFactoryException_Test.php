<?php

use Orm\InvalidServiceFactoryException;

/**
 * @covers Orm\InvalidServiceFactoryException
 */
class InvalidServiceFactoryException_Test extends TestCase
{

	public function test()
	{
		$e = new InvalidServiceFactoryException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\InvalidServiceFactoryException', $e);
	}

}
