<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

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
		$r = new Repository_createMapper\Repository_createMapperRepository($this->m);
		$this->assertInstanceOf('Repository_createMapper\Repository_createMapperMapper', $r->getMapper());
	}

}
