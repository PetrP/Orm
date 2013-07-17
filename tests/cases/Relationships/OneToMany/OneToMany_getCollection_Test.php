<?php

use Orm\RepositoryContainer;


/**
 * @covers Orm\OneToMany::getCollection
 */
class OneToMany_getCollection_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IEntityCollection', $this->o2m->_getCollection());
	}

	public function testCache()
	{
		$this->assertSame($this->o2m->_getCollection(), $this->o2m->_getCollection());
	}

	public function testNotHandledParent()
	{
		$this->e->repository->remove($this->e);
		$this->o2m->loadCollection = $this->r->mapper->findById(array(10,11,12,13,20));
		$this->assertInstanceOf('Orm\ArrayCollection', $this->o2m->_getCollection());
		$this->assertSame(0, $this->o2m->_getCollection()->count());
		$this->assertSame(array(), $this->o2m->_getCollection()->fetchAll());
	}

	public function testAttachedButNotPersist()
	{
		$e = new OneToManyX_Entity;
		$this->e->repository->attach($e);
		$e->many->loadCollection = $this->r->mapper->findById(array(10,11,12,13,20));
		$this->assertInstanceOf('Orm\ArrayCollection', $e->many->_getCollection());
		$this->assertSame(0, $e->many->_getCollection()->count());
		$this->assertSame(array(), $e->many->_getCollection()->fetchAll());
	}

	public function testAdd()
	{
		$this->o2m->add($e = new OneToMany_Entity2);
		$this->t(10,11,12,13,$e);
	}

	public function testAdd2()
	{
		$this->o2m->add($e = new OneToMany_Entity2);
		$e->param = NULL;
		$this->t(10,11,12,13);
	}

	public function testRemove()
	{
		$this->o2m->remove(11);
		$this->t(10,12,13);
	}

	public function testRemove2()
	{
		$e = new OneToMany_Entity2;
		$this->o2m->add($e);
		$this->o2m->persist();
		$this->o2m->remove($e);
		$this->t(10,11,12,13);
	}

	public function testChange()
	{
		$e = $this->r->getById(11);
		$e->param = new OneToManyX_Entity;
		$this->t(10,12,13);
	}

	public function testBadLoadCollectionReturn()
	{
		$this->o2m->loadCollection = $this;
		$this->setExpectedException('Orm\BadReturnException', "OneToMany_OneToMany::loadCollection() must return Orm\\IEntityCollection, 'OneToMany_getCollection_Test' given.");
		$this->o2m->get();
	}

	public function testOrder()
	{
		$m = new RepositoryContainer;
		$r = $m->getRepository('OneToMany_persist_order_1_Repository');
		$e = $r->attach(new OneToMany_persist_order_1_Entity);
		$create = function ($string) {
			$e = new OneToMany_persist_order_2_Entity;
			$e->string = $string;
			return $e;
		};

		$e->many->orderProperty = 'order';
		$e->many->add($a = $create(1));
		$e->many->add($b = $create(2));
		$e->many->add($c = $create(3));
		$r->flush();

		$this->assertSame(1, $a->order);
		$this->assertSame(2, $b->order);
		$this->assertSame(3, $c->order);

		$a->order = 3;
		$b->order = 2;
		$c->order = 1;

		$this->assertSame(array($c, $b, $a), $e->many->get()->fetchAll());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'getCollection');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
