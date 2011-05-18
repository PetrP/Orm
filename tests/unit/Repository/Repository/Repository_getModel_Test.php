<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\Repository::getModel
 * @covers Orm\Repository::__construct
 */
class Repository_getModel_Test extends TestCase
{

	public function test()
	{
		$m = new RepositoryContainer;
		$r = new TestsRepository($m);
		$this->assertSame($m, $r->getModel());
	}

}
