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
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		$c = 'Repository_createMapper\Repository_createMapperRepository'; // aby nebyl parse error v php52
		$r = new $c(new RepositoryContainer);
		$this->assertSame('repository_createmapper\repository_createmapper', $r->getRepositoryName());
	}

}
