<?php

/**
 * @covers Orm\ManyToMany::persist
 * @covers Orm\ArrayManyToManyMapper::add
 * @covers Orm\ArrayManyToManyMapper::remove
 */
class ManyToMany_persist_Test extends ManyToMany_Test
{

	private function tt()
	{
		$this->assertAttributeSame(NULL, 'get', $this->m2m);
		$this->assertAttributeEmpty('del', $this->m2m);
		$this->assertAttributeEmpty('add', $this->m2m);
	}

	public function testNotPersistedParent()
	{
		$this->e->getRepository()->remove($this->e);
		$this->setExpectedException('Orm\EntityNotAttachedException', 'ManyToMany_Entity is not attached to repository.');
		$this->m2m->persist();
	}

	public function testRemove()
	{
		$e = $this->m2m->remove(11);
		$e->string = 'poi';
		$this->assertTrue(isset($e->id));
		$this->assertTrue($e->isChanged());
		$this->m2m->persist();
		$this->assertTrue(isset($e->id));
		$this->assertTrue($e->isChanged());
		$this->tt();
		$this->t(10,12,13);
	}

	public function testAdd()
	{
		$e = $this->m2m->add(new OneToMany_Entity2);
		$this->assertFalse(isset($e->id));
		$this->m2m->persist();
		$this->assertTrue(isset($e->id));
		$this->tt();
		$this->t(10,11,12,13,$e->id);
	}

	public function testCascade()
	{
		$e = $this->m2m->_getCollection()->getById(11);
		$e->string = 'poi';
		$this->assertTrue($e->isChanged());
		$this->m2m->persist();
		$this->assertFalse($e->isChanged());
	}

	public function testCascade_not()
	{
		$e = $this->r->getById(11);
		$e->string = 'poi';
		$this->assertTrue($e->isChanged());
		$this->m2m->persist();
		$this->assertTrue($e->isChanged());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'persist');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
