<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::persist
 * @covers Orm\Repository::getFkForEntity
 */
class Repository_persist_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->Repository_persist_;
	}

	public function testInsert()
	{
		$e = new TestEntity;
		$this->assertSame($e, $this->r->persist($e));
		$this->assertSame(3, $e->id);
		$this->assertSame($e, $this->r->getById(3));
		$this->assertSame(1, $this->r->mapper->count);
	}

	public function testNotChanged()
	{
		$e = $this->r->getById(1);
		$this->assertSame($e, $this->r->persist($e));
		$this->assertSame(0, $this->r->mapper->count);
	}

	public function testUpdate()
	{
		$e = $this->r->getById(2);
		$e->string = 'xxx';
		$this->assertSame($e, $this->r->persist($e));
		$this->assertSame(2, $e->id);
		$this->assertSame($e, $this->r->getById(2));
		$this->assertSame(1, $this->r->mapper->count);
	}

	public function testBadEntity()
	{
		$this->setExpectedException('Orm\InvalidEntityException', "Repository_persist_Repository can't work with entity 'Repository_persist_Entity', only with 'TestEntity'");
		$this->r->persist(new Repository_persist_Entity);
	}

	public function testMapperError()
	{
		$this->r->mapper->returnNull = true;
		$this->setExpectedException('Orm\BadReturnException', "Repository_persist_Mapper::persist() must return TRUE; something wrong with mapper.");
		$this->r->persist(new TestEntity);
	}

	public function testRightEntityFromAnotherRepository()
	{
		$e = $this->r->model->tests->getById(1);
		$this->setExpectedException('Orm\InvalidEntityException', "TestEntity#1 is attached to another repository.");
		$this->r->persist($e);
	}

	public function testChangedDuringPersist()
	{
		$this->r = new Repository_persist2_Repository($this->r->model);
		$e = $this->r->getById(1);
		$e->string = 'xxx';
		$this->assertSame($e, $this->r->persist($e));
		$this->assertSame(2, $this->r->mapper->count);
		$this->assertSame('xxx_changedDuringPersist', $e->string);
		$this->assertFalse($e->isChanged());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'persist');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
