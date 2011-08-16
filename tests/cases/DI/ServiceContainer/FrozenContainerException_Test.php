<?php

use Orm\FrozenContainerException;

/**
 * @covers Orm\FrozenContainerException
 */
class FrozenContainerException_Test extends TestCase
{

	public function test()
	{
		$e = new FrozenContainerException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\FrozenContainerException', $e);
	}

}
