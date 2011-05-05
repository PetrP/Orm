<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Repository::getMapper
 */
class Repository_getMapper_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new Model;
	}

	public function testBadMapper()
	{
		$r = new Repository_getMapper_BadMapper_Repository($this->m);
		$this->setExpectedException('InvalidStateException', 'Mapper Repository_getMapper_BadMapper_Mapper must implement IMapper');
		$r->getMapper();
	}

	public function testBadMapper2()
	{
		$r = new Repository_getMapper_BadMapper2_Repository($this->m);
		$this->setExpectedException('InvalidStateException', "Repository_getMapper_BadMapper2_Repository::createMapper() must return IMapper, 'string' given");
		$r->getMapper();
	}

	public function testOk()
	{
		$r = new TestsRepository($this->m);
		$this->assertInstanceOf('IMapper', $r->getMapper());
		$this->assertInstanceOf('TestsMapper', $r->getMapper());
		$this->assertSame($r->getMapper(), $r->getMapper());
	}
}
