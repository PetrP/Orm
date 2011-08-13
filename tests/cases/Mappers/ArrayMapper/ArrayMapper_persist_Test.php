<?php

use Orm\RepositoryContainer;
use Orm\NotImplementedException;

/**
 * @covers Orm\ArrayMapper::persist
 */
class ArrayMapper_persist_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new TestsMapper(new TestsRepository(new RepositoryContainer));
	}

	public function testHasId()
	{
		$e = $this->m->getById(1);
		$e->string = 'ppp';
		$data = $this->readAttribute($this->m, 'data');
		$this->assertSame($e, $data[1]);
		$this->assertSame(1, $this->m->persist($e));
		$data = $this->readAttribute($this->m, 'data');
		$this->assertSame($e, $data[1]);
		$storage = $this->readAttribute($this->m, 'array');
		$this->assertSame(array('id' => 1), $storage[1]);
	}

	public function testNew()
	{
		$e = new TestEntity;
		$this->assertFalse(isset($data[3]));
		$this->assertSame(3, $this->m->persist($e));
		$data = $this->readAttribute($this->m, 'data');
		$this->assertSame($e, $data[3]);
		$storage = $this->readAttribute($this->m, 'array');
		$this->assertTrue(array_key_exists(3, $storage));
		$this->assertNull($storage[3]);
	}

	public function testNotImplemented()
	{
		$m = new ArrayMapper_saveData_ArrayMapper($this->m->repository);
		try {
			$m->persist(new TestEntity);
		} catch (NotImplementedException $e) {}
		$data = $this->readAttribute($this->m, 'data');
		$this->assertFalse(isset($data[3]));
		$this->setExpectedException('Orm\NotImplementedException', 'ArrayMapper_saveData_ArrayMapper::saveData() is not implement, you must override and implement that method');
		throw $e;
	}

	public function testNotInData()
	{
		$m = new TestsMapper(new TestsRepository(new RepositoryContainer));
		$this->assertSame(3, $m->persist(new TestEntity));
		$this->assertSame(4, $m->persist($e = new TestEntity));
		$id = $this->m->persist($e); // je z jineho mapperu
		$this->assertSame(3, $id); // najde mu nove id
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'persist');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
