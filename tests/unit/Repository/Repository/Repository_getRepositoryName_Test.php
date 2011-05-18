<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\Repository::getRepositoryName
 * @covers Orm\Repository::__construct
 */
class Repository_getRepositoryName_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$this->assertSame('tests', $r->getRepositoryName());
	}

}
