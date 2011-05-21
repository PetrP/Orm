<?php

use Orm\RepositoryContainer;
use Nette\NotImplementedException;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\ArrayMapper::remove
 */
class ArrayMapper_remove_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new TestsMapper(new TestsRepository(new RepositoryContainer));
	}

	public function test()
	{
		$e = $this->m->getById(1);
		$e->string = 'ppp';
		$this->assertSame(true, $this->m->remove($e));
		$data = $this->readAttribute($this->m, 'data');
		$this->assertSame(NULL, $data[1]);
		$storage = $this->readAttribute($this->m, 'array');
		$this->assertSame(array('id' => 1), $storage[1]);
	}

	public function testNew()
	{
		$e = new TestEntity;
		$this->setExpectedException('Nette\InvalidStateException', 'You must persist entity first');
		$this->m->remove($e);
	}

	public function testNotImplemented()
	{
		$m = new ArrayMapper_loadData_ArrayMapper($this->m->repository);
		try {
			$m->remove($this->m->getById(1));
		} catch (NotImplementedException $e) {}
		$data = $this->readAttribute($this->m, 'data');
		$this->assertFalse(isset($data[3]));
		$this->setExpectedException('Nette\NotImplementedException', 'ArrayMapper_loadData_ArrayMapper::loadData() is not implement, you must override and implement that method');
		throw $e;
	}

}