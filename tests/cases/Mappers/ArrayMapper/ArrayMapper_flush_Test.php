<?php

use Orm\RepositoryContainer;
use Orm\ValidationHelper;

/**
 * @covers Orm\ArrayMapper::flush
 */
class ArrayMapper_flush_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->m = $m->tests->mapper;
	}

	public function testPersist()
	{
		$e = new TestEntity;
		$e->string = 'xxx';
		$this->m->getById(2)->string = 'uuu';
		$this->m->persist($e);
		$this->m->flush();
		$storage = $this->readAttribute($this->m, 'array');
		$this->assertSame(3, count($storage));
		$this->assertSame(array(
			'id' => 1,
			'string' => '',
			'date' => ValidationHelper::createDateTime('now')->format('c'),
		), $storage[1]);
		$this->assertSame(array(
			'id' => 2,
			'string' => 'uuu', // todo funguje auto flush
			'date' => ValidationHelper::createDateTime('now')->format('c'),
		), $storage[2]);
		$this->assertSame(array(
			'id' => NULL, // NULL becouse id fill Repository and i call direct mapper
			'string' => 'xxx',
			'date' => ValidationHelper::createDateTime('now')->format('c'),
		), $storage[3]);
	}

	public function testRemove()
	{
		$this->m->remove($this->m->getById(1));
		$this->m->remove($this->m->getById(2));

		$this->m->repository->persist(new TestEntity);
		$this->m->remove($this->m->getById(3));

		$this->m->flush();
		$storage = $this->readAttribute($this->m, 'array');
		$this->assertSame(3, count($storage));
		$this->assertSame(NULL, $storage[1]);
		$this->assertSame(NULL, $storage[2]);
		$this->assertSame(NULL, $storage[3]);

	}

	public function testNotImplemented()
	{
		$m = new ArrayMapper_flush_saveData_ArrayMapper($this->m->repository);
		$m->persist(new TestEntity);
		$this->setExpectedException('Orm\NotImplementedException', 'ArrayMapper_flush_saveData_ArrayMapper::saveData() is not implement, you must override and implement that method');
		$m->flush();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'flush');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
