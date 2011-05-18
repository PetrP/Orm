<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\Mapper::getRepository
 * @covers Orm\Mapper::__construct
 */
class Mapper_getRepository_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$m = new TestsMapper($r);
		$this->assertSame($r, $m->getRepository());
	}

}
