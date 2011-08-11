<?php

/**
 * @covers Orm\OneToMany::has
 */
class OneToMany_has_Test extends OneToMany_Test
{

	public function testNo()
	{
		$this->assertFalse($this->o2m->has(new OneToMany_Entity));
		$this->assertFalse($this->o2m->has($this->r->getById(999)));
		$this->assertFalse($this->o2m->has($this->r->getById(20)));
		$this->assertFalse($this->o2m->has(999));
		$this->assertFalse($this->o2m->has(20));
		$this->assertFalse($this->o2m->has(array()));
		$this->assertFalse($this->o2m->has(array('aaa' => 'bbb')));
		$this->assertFalse($this->o2m->has(array('id' => 999)));
		$this->assertFalse($this->o2m->has(array('id' => 20)));
		$this->assertFalse($this->o2m->has(NULL));
		$this->assertFalse($this->o2m->has(new TestEntity));
		$this->assertFalse($this->o2m->has($this->r->model->tests->getById(20)));
	}

	public function testYes()
	{
		$this->assertTrue($this->o2m->has($this->r->getById(10)));
		$this->assertTrue($this->o2m->has(11));
		$this->assertTrue($this->o2m->has(array('id' => 12)));
	}

	public function testNew()
	{
		$e = new OneToMany_Entity;
		$this->o2m->add($e);
		$this->assertTrue($this->o2m->has($e));
		$this->o2m->remove($e);
		$this->assertFalse($this->o2m->has($e));
		$this->o2m->add($e);
		$this->assertTrue($this->o2m->has($e));
	}

	public function testExist()
	{
		$this->o2m->remove(13);
		$this->o2m->remove(11);
		$this->assertFalse($this->o2m->has(13));
		$this->assertFalse($this->o2m->has(11));
		$this->o2m->add(13);
		$this->assertTrue($this->o2m->has(13));
		$this->o2m->add(11);
		$this->assertTrue($this->o2m->has(11));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'has');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
