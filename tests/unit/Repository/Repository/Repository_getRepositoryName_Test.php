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

	public function testNamespace()
	{
		$r = new Repository_createMapper\Repository_createMapperRepository(new RepositoryContainer);
		$this->assertSame('repository_createmapper\repository_createmapper', $r->getRepositoryName());
	}

}
