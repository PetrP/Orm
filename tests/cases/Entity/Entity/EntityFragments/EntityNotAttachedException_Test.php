<?php

use Orm\EntityNotAttachedException;

/**
 * @covers Orm\EntityNotAttachedException
 */
class EntityNotAttachedException_Test extends TestCase
{

	public function test()
	{
		$e = new EntityNotAttachedException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\EntityNotAttachedException', $e);
	}

}
