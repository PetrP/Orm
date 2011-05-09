<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers ManyToMany::get
 * @covers ArrayManyToManyMapper::load
 */
class ManyToMany_get_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertInstanceOf('IEntityCollection', $this->m2m->get());
	}

	public function testCache()
	{
		$this->assertSame($this->m2m->get(), $this->m2m->get());
	}

	public function testNotHandledParent()
	{
		$this->e->generatingRepository->remove($this->e);
		$this->assertInstanceOf('ArrayCollection', $this->m2m->get());
		$this->assertSame(array(), $this->m2m->get()->fetchAll());
	}

	public function testAdd()
	{
		$this->m2m->add($e = new OneToMany_Entity2);
		$this->t(10,11,12,13,$e);
	}

	public function testRemove()
	{
		$this->m2m->remove(11);
		$this->t(10,12,13);
	}

}
