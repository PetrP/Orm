<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers OneToMany::get
 */
class OneToMany_get_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertInstanceOf('IEntityCollection', $this->o2m->get());
	}

	public function testCache()
	{
		$this->assertSame($this->o2m->get(), $this->o2m->get());
	}

	public function testFindByRepo()
	{
		$o2m = new OneToMany($this->e, $this->e->model->OneToMany_2, 'param');
		$o2m->get();
		$this->assertSame(1, $this->e->model->OneToMany_2->count);
		$this->assertSame(0, $this->e->model->OneToMany_2->mapper->count);
	}

	public function testFindByMapper()
	{
		$o2m = new OneToMany($this->e, $this->e->model->OneToMany_3, 'param');
		$o2m->get();
		$this->assertSame(1, $this->e->model->OneToMany_3->mapper->count);
	}

	public function testNotHandledParent()
	{
		$this->e->generatingRepository->remove($this->e);
		$this->assertInstanceOf('ArrayCollection', $this->o2m->get());
		$this->assertSame(array(), $this->o2m->get()->fetchAll());
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
