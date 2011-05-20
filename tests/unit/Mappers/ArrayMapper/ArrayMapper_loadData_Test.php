<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\ArrayMapper::loadData
 */
class ArrayMapper_loadData_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new ArrayMapper_loadData_ArrayMapper(new TestsRepository(new RepositoryContainer));
	}

	public function testReturn()
	{
		$this->setExpectedException('Nette\NotImplementedException', 'ArrayMapper_loadData_ArrayMapper::loadData() is not implement, you must override and implement that method');
		$this->m->findAll();
	}

}
