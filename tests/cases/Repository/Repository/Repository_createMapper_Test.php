<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::createMapper
 */
class Repository_createMapper_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$r = new TestsRepository($this->m);
		$this->assertInstanceOf('TestsMapper', $r->getMapper());
	}

	public function testDefault()
	{
		$r = new Repository_DefaultMapper_Repository($this->m);
		$this->assertInstanceOf('Orm\DibiMapper', $r->getMapper());
	}

	public function testNamespace()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		$c = 'Repository_createMapper\Repository_createMapperRepository'; // aby nebyl parse error v php52
		$r = new $c($this->m);
		$this->assertInstanceOf('Repository_createMapper\Repository_createMapperMapper', $r->getMapper());
	}

}
