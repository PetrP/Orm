<?php

/**
 * @covers Orm\ManyToMany::getModel
 */
class ManyToMany_getModel_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertSame($this->e->getModel(), $this->m2m->getModel());
	}

	public function testNotHas()
	{
		$this->e->fireEvent('onAfterRemove', $this->e->repository);
		$this->setExpectedException('Orm\EntityNotAttachedException', 'ManyToMany_Entity is not attached to repository.');
		$this->m2m->getModel();
	}

	public function testDontNeedHas()
	{
		$this->assertSame($this->e->getModel(), $this->m2m->getModel(false));
	}

	public function testDontNeedNotHas()
	{
		$this->e->fireEvent('onAfterRemove', $this->e->repository);
		$this->assertSame(NULL, $this->m2m->getModel(false));
	}

	public function testDontNeedNotHasNull()
	{
		$this->e->fireEvent('onAfterRemove', $this->e->repository);
		$this->assertSame(NULL, $this->m2m->getModel(NULL));
	}

	public function testNeed()
	{
		$this->assertSame($this->e->getModel(), $this->m2m->getModel(true));
	}

	public function testNeedNotHas()
	{
		$this->e->fireEvent('onAfterRemove', $this->e->repository);
		$this->setExpectedException('Orm\EntityNotAttachedException', 'ManyToMany_Entity is not attached to repository.');
		$this->m2m->getModel(true);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'getModel');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
