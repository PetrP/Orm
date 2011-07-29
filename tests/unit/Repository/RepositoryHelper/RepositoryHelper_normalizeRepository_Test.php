<?php

use Orm\RepositoryContainer;
use Orm\RepositoryHelper;

/**
 * @covers Orm\RepositoryHelper::normalizeRepository
 */
class RepositoryHelper_normalizeRepository_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$h = new RepositoryHelper;
		$this->assertSame('tests', $h->normalizeRepository($r));
	}

	public function testNamespace()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		$c = 'Repository_createMapper\Repository_createMapperRepository'; // aby nebyl parse error v php52
		$r = new $c(new RepositoryContainer);
		$h = new RepositoryHelper;
		$this->assertSame('repository_createmapper\repository_createmapper', $h->normalizeRepository($r));
	}

}
