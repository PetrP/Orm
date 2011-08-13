<?php

use Orm\RepositoryAlreadyRegisteredException;

/**
 * @covers Orm\RepositoryAlreadyRegisteredException
 */
class RepositoryAlreadyRegisteredException_Test extends TestCase
{

	public function test()
	{
		$e = new RepositoryAlreadyRegisteredException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\RepositoryAlreadyRegisteredException', $e);
	}

}
