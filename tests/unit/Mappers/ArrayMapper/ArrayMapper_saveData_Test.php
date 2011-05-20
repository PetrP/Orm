<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\ArrayMapper::saveData
 */
class ArrayMapper_saveData_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new ArrayMapper_saveData_ArrayMapper(new TestsRepository(new RepositoryContainer));
	}

	public function testReturn()
	{
		$this->setExpectedException('Nette\NotImplementedException', 'ArrayMapper_saveData_ArrayMapper::saveData() is not implement, you must override and implement that method');
		$this->m->persist(new TestEntity);
	}

}
