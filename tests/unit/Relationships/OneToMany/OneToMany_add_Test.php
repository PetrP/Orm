<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\OneToMany::add
 */
class OneToMany_add_Test extends OneToMany_Test
{

	public function testNew()
	{
		$e = new OneToMany_Entity;
		$this->o2m->add($e);
		$this->t(10,11,12,13, $e);
		$this->o2m->remove($e);
		$this->t(10,11,12,13);
		$this->o2m->add($e);
		$this->t(10,11,12,13, $e);
	}

	public function testExist()
	{
		$this->o2m->remove(13);
		$this->o2m->remove(11);
		$this->t(10,12);
		$this->o2m->add(13);
		$this->t(10,12,13);
		$this->o2m->add(11);
		$this->t(10,12,13,11);
	}

	public function testMultipleSame()
	{
		$this->o2m->remove(11);
		$this->t(10,12,13);
		$this->o2m->add(11);
		$this->t(10,12,13,11);
		$this->o2m->add(11);
		$this->t(10,12,13,11);
	}

	public function testBad()
	{
		$this->setExpectedException('UnexpectedValueException', "OneToMany_Repository can't work with entity 'TestEntity'");
		$this->o2m->add(new TestEntity);
	}

	public function testBad2()
	{
		$e = new OneToMany_Entity;
		$e->param = new TestEntity;
		$this->setExpectedException('UnexpectedValueException', 'Entity OneToMany_Entity is already asociated with another entity.');
		$this->o2m->add($e);
	}

}
