<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::onAttachModel
 */
class AttachableEntityFragment_onAttachModel_Test extends TestCase
{
	private $m1;
	private $m2;
	private $r1;
	private $r2;

	protected function setUp()
	{
		$this->m1 = new RepositoryContainer;
		$this->m2 = new RepositoryContainer;
		$this->r1 = $this->m1->testentityrepository;
		$this->r2 = $this->m2->testentityrepository;
	}

	public function test()
	{
		$e = new TestEntity;

		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame(NULL, $e->getModel(false));

		$e->fireEvent('onAttachModel', NULL, $this->m1);

		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame($this->m1, $e->getModel(false));
	}

	public function testDifferentRC1()
	{
		$e = new TestEntity;

		$e->fireEvent('onAttachModel', NULL, $this->m1);
		$this->setExpectedException('Orm\EntityAlreadyAttachedException', 'TestEntity is already attached to another RepositoryContainer.');
		$e->fireEvent('onAttachModel', NULL, $this->m2);
	}

	public function testDifferentRC2()
	{
		$e = new TestEntity;

		$e->fireEvent('onAttach', $this->r1);
		$this->setExpectedException('Orm\EntityAlreadyAttachedException', 'TestEntity is already attached to another RepositoryContainer.');
		$e->fireEvent('onAttachModel', NULL, $this->m2);
	}

	public function testTwiceOk()
	{
		$e = new TestEntity;
		$e->fireEvent('onAttachModel', NULL, $this->m1);
		$e->fireEvent('onAttachModel', NULL, $this->m1);

		$e = new TestEntity;
		$e->fireEvent('onAttach', $this->r1);
		$e->fireEvent('onAttachModel', NULL, $this->m1);

		$this->assertTrue(true);
	}

	public function testErrorAfterRemove()
	{
		$m = new RepositoryContainer;
		$r = $m->getRepository('TestEntityRepository');
		$e = new TestEntity;
		$r->attach($e);
		$r->remove($e);
		$this->setExpectedException('Orm\EntityWasRemovedException', 'TestEntity was removed. Clone entity before reattach to repository.');
		$e->fireEvent('onAttachModel', NULL, $m);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AttachableEntityFragment', 'onAttachModel');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
