<?php

use Orm\RepositoryNotFoundException;

/**
 * @covers Orm\RepositoryNotFoundException
 */
class RepositoryNotFoundException_Test extends TestCase
{

	public function test()
	{
		$e = new RepositoryNotFoundException;
		$this->assertInstanceOf('RuntimeException', $e);
		$this->assertInstanceOf('Orm\RepositoryNotFoundException', $e);
	}

}
