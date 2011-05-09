<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Repository::createMapper
 */
class Repository_createMapper_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new Model;
	}

	public function test()
	{
		$r = new TestsRepository($this->m);
		$this->assertInstanceOf('TestsMapper', $r->getMapper());
	}

	public function testDefault()
	{
		$r = new Repository_DefaultMapper_Repository($this->m);
		$this->assertInstanceOf('DibiMapper', $r->getMapper());
	}

}
