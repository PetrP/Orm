<?php

/**
 * @covers Orm\ManyToMany::has
 */
class ManyToMany_has_Test extends ManyToMany_Test
{

	public function testNo()
	{
		$this->assertFalse($this->m2m->has(new OneToMany_Entity));
		$this->assertFalse($this->m2m->has($this->r->getById(999)));
		$this->assertFalse($this->m2m->has($this->r->getById(20)));
		$this->assertFalse($this->m2m->has(999));
		$this->assertFalse($this->m2m->has(20));
		$this->assertFalse($this->m2m->has(array()));
		$this->assertFalse($this->m2m->has(array('aaa' => 'bbb')));
		$this->assertFalse($this->m2m->has(array('id' => 999)));
		$this->assertFalse($this->m2m->has(array('id' => 20)));
		$this->assertFalse($this->m2m->has(NULL));
		$this->assertFalse($this->m2m->has(new TestEntity));
		$this->assertFalse($this->m2m->has($this->r->model->tests->getById(20)));
	}

	public function testYes()
	{
		$this->assertTrue($this->m2m->has($this->r->getById(10)));
		$this->assertTrue($this->m2m->has(11));
		$this->assertTrue($this->m2m->has(array('id' => 12)));
	}

	public function testNew()
	{
		$e = new OneToMany_Entity;
		$this->m2m->add($e);
		$this->assertTrue($this->m2m->has($e));
		$this->m2m->remove($e);
		$this->assertFalse($this->m2m->has($e));
		$this->m2m->add($e);
		$this->assertTrue($this->m2m->has($e));
	}

	public function testExist()
	{
		$this->m2m->remove(13);
		$this->m2m->remove(11);
		$this->assertFalse($this->m2m->has(13));
		$this->assertFalse($this->m2m->has(11));
		$this->m2m->add(13);
		$this->assertTrue($this->m2m->has(13));
		$this->m2m->add(11);
		$this->assertTrue($this->m2m->has(11));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'has');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
