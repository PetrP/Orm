<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\OneToMany::createEntity
 * @covers Orm\BaseToMany::createEntity
 */
class OneToMany_createEntity_Test extends OneToMany_Test
{
	private function tt($enter)
	{
		return $this->o2m->__createEntity($enter);
	}

	public function testEntity()
	{
		$e = new OneToMany_Entity;
		$this->assertSame($e, $this->tt($e));
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testBad()
	{
		$this->setExpectedException('Orm\InvalidEntityException', "OneToMany_Repository can't work with entity 'TestEntity'");
		$this->tt(new TestEntity);
	}

	public function testId()
	{
		$e = $this->r->getById(11);
		$this->assertSame($e, $this->tt(11));
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testIdNotFound()
	{
		$this->setExpectedException('Orm\EntityNotFoundException', 'Entity \'333\' not found in `OneToMany_Repository`');
		$this->tt(333);
	}

	public function testArray()
	{
		$e = $this->tt(array('string' => 'xyz'));
		$this->assertInstanceOf('OneToMany_Entity', $e);
		$this->assertFalse(isset($e->id));
		$this->assertSame('xyz', $e->string);
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testArrayWithId()
	{
		$e = $this->r->getById(11);
		$ee = $this->tt(array('id' => 11, 'string' => 'xyz'));
		$this->assertSame($e, $ee);
		$this->assertSame('xyz', $e->string);
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testArrayWithIdNotFound()
	{
		$e = $this->tt(array('id' => 333, 'string' => 'xyz'));
		$this->assertInstanceOf('OneToMany_Entity', $e);
		$this->assertFalse(isset($e->id));
		$this->assertSame('xyz', $e->string);
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testTraversable()
	{
		$e = $this->tt(new DibiRow(array('string' => 'xyz')));
		$this->assertInstanceOf('OneToMany_Entity', $e);
		$this->assertFalse(isset($e->id));
		$this->assertSame('xyz', $e->string);
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testAttach1()
	{
		$m = $this->r->model;
		$r = $this->e->getRepository();
		$r2 = $this->r;
		$e = new OneToManyX_Entity;
		$e2 = $r2->attach(new OneToMany_Entity);

		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame(NULL, $e->getModel(false));
		$this->assertSame($r2, $e2->getRepository(false));
		$this->assertSame($m, $e2->getModel(false));

		$e->many->__createEntity($e2);

		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame($m, $e->getModel(false));
		$this->assertSame($r2, $e2->getRepository(false));
		$this->assertSame($m, $e2->getModel(false));
	}

	public function testAttach2()
	{
		$m = $this->r->model;
		$r = $this->e->getRepository();
		$r2 = $this->r;
		$e = $r->getById(1);
		$e2 = new OneToMany_Entity;

		$this->assertSame($r, $e->getRepository(false));
		$this->assertSame($m, $e->getModel(false));
		$this->assertSame(NULL, $e2->getRepository(false));
		$this->assertSame(NULL, $e2->getModel(false));

		$e->many->__createEntity($e2);

		$this->assertSame($r, $e->getRepository(false));
		$this->assertSame($m, $e->getModel(false));
		$this->assertSame($r2, $e2->getRepository(false));
		$this->assertSame($m, $e2->getModel(false));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'createEntity');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
