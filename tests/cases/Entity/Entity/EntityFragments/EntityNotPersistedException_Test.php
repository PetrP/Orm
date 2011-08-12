<?php

use Orm\EntityNotPersistedException;

/**
 * @covers Orm\EntityNotPersistedException
 */
class EntityNotPersistedException_Test extends TestCase
{

	public function test()
	{
		$e = new EntityNotPersistedException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\EntityNotPersistedException', $e);
	}

}
