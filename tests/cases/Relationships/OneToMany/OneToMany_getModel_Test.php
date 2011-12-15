<?php

/**
 * @covers Orm\OneToMany::getModel
 */
class OneToMany_getModel_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertSame($this->e->getModel(), $this->o2m->getModel());
	}

	public function testNotHas()
	{
		$this->e->fireEvent('onAfterRemove', $this->e->repository);
		$this->setExpectedException('Orm\EntityNotAttachedException', 'OneToManyX_Entity is not attached to repository.');
		$this->o2m->getModel();
	}

	public function testDontNeedHas()
	{
		$this->assertSame($this->e->getModel(), $this->o2m->getModel(false));
	}

	public function testDontNeedNotHas()
	{
		$this->e->fireEvent('onAfterRemove', $this->e->repository);
		$this->assertSame(NULL, $this->o2m->getModel(false));
	}

	public function testDontNeedNotHasNull()
	{
		$this->e->fireEvent('onAfterRemove', $this->e->repository);
		$this->assertSame(NULL, $this->o2m->getModel(NULL));
	}

	public function testNeed()
	{
		$this->assertSame($this->e->getModel(), $this->o2m->getModel(true));
	}

	public function testNeedNotHas()
	{
		$this->e->fireEvent('onAfterRemove', $this->e->repository);
		$this->setExpectedException('Orm\EntityNotAttachedException', 'OneToManyX_Entity is not attached to repository.');
		$this->o2m->getModel(true);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'getModel');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
