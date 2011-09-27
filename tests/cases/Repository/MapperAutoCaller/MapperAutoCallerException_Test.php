<?php

use Orm\MapperAutoCallerException;

/**
 * @covers Orm\MapperAutoCallerException
 */
class MapperAutoCallerException_Test extends TestCase
{

	public function test()
	{
		$e = new MapperAutoCallerException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\MapperAutoCallerException', $e);
	}

}
