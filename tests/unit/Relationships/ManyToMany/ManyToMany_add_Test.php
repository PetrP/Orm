<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\ManyToMany::add
 */
class ManyToMany_add_Test extends ManyToMany_Test
{

	public function testNew()
	{
		$e = new OneToMany_Entity;
		$this->m2m->add($e);
		$this->t(10,11,12,13, $e);
		$this->m2m->remove($e);
		$this->t(10,11,12,13);
		$this->m2m->add($e);
		$this->t(10,11,12,13, $e);
	}

	public function testExist()
	{
		$this->m2m->remove(13);
		$this->m2m->remove(11);
		$this->t(10,12);
		$this->m2m->add(13);
		$this->t(10,12,13);
		$this->m2m->add(11);
		$this->t(10,12,13,11);
	}

	public function testMultipleSame()
	{
		$this->m2m->remove(11);
		$this->t(10,12,13);
		$this->m2m->add(11);
		$this->t(10,12,13,11);
		$this->m2m->add(11);
		$this->t(10,12,13,11);
	}

	public function testBad()
	{
		$this->setExpectedException('UnexpectedValueException', "OneToMany_Repository can't work with entity 'TestEntity'");
		$this->m2m->add(new TestEntity);
	}

}
