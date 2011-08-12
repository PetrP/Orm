<?php

/**
 * @covers Orm\ManyToMany::remove
 */
class ManyToMany_remove_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->m2m->remove(12);
		$this->t(10,11,13);
		$this->m2m->remove(10);
		$this->t(11,13);
	}

	public function test2()
	{
		$this->m2m->remove(11);
		$this->t(10,12,13);
		$this->m2m->add(11);
		$this->t(10,12,13,11);
		$this->m2m->remove(11);
		$this->t(10,12,13);
	}

	public function testNew()
	{
		$e = new OneToMany_Entity;
		$this->m2m->add($e);
		$this->t(10,11,12,13,$e);
		$this->m2m->remove($e);
		$this->t(10,11,12,13);
	}

	public function testMultipleSame()
	{
		$this->m2m->remove(11);
		$this->m2m->remove(11);
		$this->assertTrue(true);
	}

	public function testBad()
	{
		$this->setExpectedException('UnexpectedValueException', "OneToMany_Repository can't work with entity 'TestEntity'");
		$this->m2m->remove(new TestEntity);
	}

}
