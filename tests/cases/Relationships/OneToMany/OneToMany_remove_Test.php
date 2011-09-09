<?php

/**
 * @covers Orm\OneToMany::remove
 */
class OneToMany_remove_Test extends OneToMany_Test
{

	public function test()
	{
		$this->o2m->remove(12);
		$this->t(10,11,13);
		$this->o2m->remove(10);
		$this->t(11,13);
	}

	public function test2()
	{
		$this->o2m->remove(11);
		$this->t(10,12,13);
		$this->o2m->add(11);
		$this->t(10,12,13,11);
		$this->o2m->remove(11);
		$this->t(10,12,13);
	}

	public function testNew()
	{
		$e = new OneToMany_Entity;
		$this->o2m->add($e);
		$this->t(10,11,12,13,$e);
		$this->o2m->remove($e);
		$this->t(10,11,12,13);
	}

	public function testMultipleSame()
	{
		$e = $this->o2m->_getCollection()->getById(11);
		$p = $e->param;
		$this->o2m->remove(11);
		$ee = $this->o2m->remove(11);
		// nevyhodi chybu protoze param nemuze byt null tak se nesmaze
		$this->assertSame($e, $ee);
		$this->assertSame($p, $e->param);
	}

	public function testMultipleSame2()
	{
		$e = new OneToMany_Entity2;
		$this->o2m->add($e);
		$this->assertSame($this->e, $e->param);
		$this->o2m->remove($e);
		$this->assertSame(NULL, $e->param);
		$this->setExpectedException('UnexpectedValueException', 'Entity OneToMany_Entity2 is not asociated with this entity.');
		$this->o2m->remove($e);
	}

	public function testBad()
	{
		$this->setExpectedException('UnexpectedValueException', "OneToMany_Repository can't work with entity 'TestEntity'");
		$this->o2m->remove(new TestEntity);
	}

	public function testBad2()
	{
		$e = $this->o2m->_getCollection()->getById(11);
		$e->param = new TestEntity;
		$this->setExpectedException('UnexpectedValueException', 'Entity OneToMany_Entity#11 is not asociated with this entity.');
		$this->o2m->remove($e);
	}

	public function testChanged()
	{
		$this->assertFalse($this->e->isChanged());
		$this->o2m->remove(11);
		$this->assertTrue($this->e->isChanged());
	}
}
