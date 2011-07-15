<?php

/**
 * @covers Orm\ManyToMany::getCollection
 * @covers Orm\ArrayManyToManyMapper::load
 */
class ManyToMany_getCollection_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IEntityCollection', $this->m2m->_getCollection());
	}

	public function testCache()
	{
		$this->assertSame($this->m2m->_getCollection(), $this->m2m->_getCollection());
	}

	public function testNotHandledParent()
	{
		$this->e->generatingRepository->remove($this->e);
		$this->assertInstanceOf('Orm\ArrayCollection', $this->m2m->_getCollection());
		$this->assertSame(array(), $this->m2m->_getCollection()->fetchAll());
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
