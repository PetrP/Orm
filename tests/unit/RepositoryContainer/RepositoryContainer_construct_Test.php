<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../boot.php';

/**
 * @covers Orm\RepositoryContainer::__construct
 */
class RepositoryContainer_construct_Test extends TestCase
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IRepositoryContainer', new RepositoryContainer);
	}

}
