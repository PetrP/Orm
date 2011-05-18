<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Mapper::getRepository
 * @covers Mapper::__construct
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
