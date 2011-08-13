<?php

use Orm\RepositoryContainer;
use Orm\NotImplementedException;

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
		$this->setExpectedException('Orm\EntityNotPersistedException', 'TestEntity is not persisted.');
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
		$this->setExpectedException('Orm\NotImplementedException', 'ArrayMapper_loadData_ArrayMapper::loadData() is not implement, you must override and implement that method');
		throw $e;
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'remove');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
