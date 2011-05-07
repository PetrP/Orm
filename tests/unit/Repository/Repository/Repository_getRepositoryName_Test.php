<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Repository::getRepositoryName
 * @covers Repository::__construct
 */
class Repository_getRepositoryName_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new Model);
		$this->assertSame('tests', $r->getRepositoryName());
	}

}
