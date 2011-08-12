<?php

use Orm\MapperJoinException;

/**
 * @covers Orm\MapperJoinException
 */
class MapperJoinException_Test extends TestCase
{

	public function test()
	{
		$e = new MapperJoinException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\MapperJoinException', $e);
	}

}
