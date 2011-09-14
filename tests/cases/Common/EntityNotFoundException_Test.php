<?php

use Orm\EntityNotFoundException;

/**
 * @covers Orm\EntityNotFoundException
 */
class EntityNotFoundException_Test extends TestCase
{

	public function test()
	{
		$e = new EntityNotFoundException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\EntityNotFoundException', $e);
	}

}
