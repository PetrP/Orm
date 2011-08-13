<?php

use Orm\PropertyAccessException;

/**
 * @covers Orm\PropertyAccessException
 */
class PropertyAccessException_Test extends TestCase
{

	public function test()
	{
		$e = new PropertyAccessException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\PropertyAccessException', $e);
	}

}
