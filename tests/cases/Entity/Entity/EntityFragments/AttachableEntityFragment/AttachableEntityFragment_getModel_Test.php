<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::getModel
 */
class AttachableEntityFragment_getModel_Test extends TestCase
{
	private $m;
	private $r;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
		$this->r = $this->m->testentityrepository;
	}

	public function testNotNeed()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getModel(false));
		$e = $this->r->getById(1);
		$this->assertSame($this->m, $e->getModel(false));
	}

	public function testNeed1()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->m, $e->getModel(true));
		$this->assertSame($this->m, $e->getModel());
	}

	public function testNeed2()
	{
		$e = new TestEntity;
		$this->setExpectedException('Orm\EntityNotAttachedException', 'TestEntity is not attached to repository.');
		$e->getModel();
	}

	public function testNeedBc()
	{
		$e = new TestEntity;
		$this->assertSame($this->m, $e->getModel(NULL));
	}

	public function testAfterRemove()
	{
		$e = $this->r->getById(1);
		$this->r->remove($e);
		$this->assertSame(NULL, $e->getModel(false));

		$this->setExpectedException('Orm\EntityNotAttachedException', 'TestEntity is not attached to repository.');
		$e->getModel();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AttachableEntityFragment', 'getModel');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
