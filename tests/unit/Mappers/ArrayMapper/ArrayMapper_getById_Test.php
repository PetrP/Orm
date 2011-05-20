<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\ArrayMapper::getById
 */
class ArrayMapper_getById_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new TestsMapper(new TestsRepository(new RepositoryContainer));
	}

	public function testReturnExists()
	{
		$this->assertInstanceOf('TestEntity', $this->m->getById(1));
	}

	public function testReturnUnExists()
	{
		$this->assertSame(NULL, $this->m->getById(666));
	}

	public function testEmpty()
	{
		$this->assertSame(NULL, $this->m->getById(''));
		$this->assertSame(NULL, $this->m->getById(NULL));
		$this->assertSame(NULL, $this->m->getById(false));
	}

}
