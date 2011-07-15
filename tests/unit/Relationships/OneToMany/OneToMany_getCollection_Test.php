<?php

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

	public function testFindByRepo()
	{
		$o2m = new OneToMany_OneToMany($this->e, $this->e->model->OneToMany_2, 'param');
		$o2m->_getCollection();
		$this->assertSame(1, $this->e->model->OneToMany_2->count);
		$this->assertSame(0, $this->e->model->OneToMany_2->mapper->count);
	}

	public function testFindByMapper()
	{
		$o2m = new OneToMany_OneToMany($this->e, $this->e->model->OneToMany_3, 'param');
		$o2m->_getCollection();
		$this->assertSame(1, $this->e->model->OneToMany_3->mapper->count);
	}

	public function testNotHandledParent()
	{
		$this->e->generatingRepository->remove($this->e);
		$this->assertInstanceOf('Orm\ArrayCollection', $this->o2m->_getCollection());
		$this->assertSame(array(), $this->o2m->_getCollection()->fetchAll());
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
		$e->param = new TestEntity;
		$this->t(10,12,13);
	}

}
