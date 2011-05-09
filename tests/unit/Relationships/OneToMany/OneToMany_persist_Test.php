<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers OneToMany::persist
 */
class OneToMany_persist_Test extends OneToMany_Test
{

	private function tt()
	{
		$this->assertAttributeSame(NULL, 'get', $this->o2m);
		$this->assertAttributeEmpty('del', $this->o2m);
		$this->assertAttributeEmpty('edit', $this->o2m);
		$this->assertAttributeEmpty('add', $this->o2m);
	}

	public function testNotPersistedParent()
	{
		$this->e->getGeneratingRepository()->remove($this->e);
		$this->setExpectedException('InvalidStateException', 'TestEntity is not attached to repository.');
		$this->o2m->persist();
	}

	public function testRemove()
	{
		$e = $this->o2m->remove(11);
		$this->assertTrue(isset($e->id));
		$this->o2m->persist();
		$this->assertFalse(isset($e->id));
		$this->tt();
		$this->t(10,12,13);
	}

	public function testRemoveParamNull()
	{
		$e = $this->o2m->add(new OneToMany_Entity2);
		$this->r->persist($e);
		$this->assertSame($this->e, $e->param);
		$this->o2m->remove($e);
		$this->assertSame(NULL, $e->param);
		$this->assertTrue($e->isChanged());
		$this->o2m->persist();
		$this->assertTrue(isset($e->id));
		$this->assertSame(NULL, $e->param);
		$this->assertFalse($e->isChanged());
		$this->tt();
		$this->t(10,11,12,13);
	}

	public function testAdd()
	{
		$e = $this->o2m->add(new OneToMany_Entity2);
		$this->assertFalse(isset($e->id));
		$this->o2m->persist();
		$this->assertTrue(isset($e->id));
		$this->tt();
		$this->t(10,11,12,13,$e->id);
	}

	public function testCascade()
	{
		$e = $this->o2m->get()->getById(11);
		$e->string = 'poi';
		$this->assertTrue($e->isChanged());
		$this->o2m->persist();
		$this->assertFalse($e->isChanged());
	}

	public function testCascade_not()
	{
		$e = $this->r->getById(11);
		$e->string = 'poi';
		$this->assertTrue($e->isChanged());
		$this->o2m->persist();
		$this->assertTrue($e->isChanged());
	}

}
