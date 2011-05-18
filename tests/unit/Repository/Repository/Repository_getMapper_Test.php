<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\Repository::getMapper
 */
class Repository_getMapper_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function testBadMapper()
	{
		$r = new Repository_getMapper_BadMapper_Repository($this->m);
		$this->setExpectedException('Nette\InvalidStateException', 'Mapper Repository_getMapper_BadMapper_Mapper must implement Orm\IMapper');
		$r->getMapper();
	}

	public function testBadMapper2()
	{
		$r = new Repository_getMapper_BadMapper2_Repository($this->m);
		$this->setExpectedException('Nette\InvalidStateException', "Repository_getMapper_BadMapper2_Repository::createMapper() must return Orm\\IMapper, 'string' given");
		$r->getMapper();
	}

	public function testOk()
	{
		$r = new TestsRepository($this->m);
		$this->assertInstanceOf('Orm\IMapper', $r->getMapper());
		$this->assertInstanceOf('TestsMapper', $r->getMapper());
		$this->assertSame($r->getMapper(), $r->getMapper());
	}
}
