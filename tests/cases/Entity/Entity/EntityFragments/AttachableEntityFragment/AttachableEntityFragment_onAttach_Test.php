<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::onAttach
 */
class AttachableEntityFragment_onAttach_Test extends TestCase
{
	private $r;

	private $m1;
	private $m2;
	private $r1;
	private $r2;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentityrepository;
		$this->m1 = $m;
		$this->m2 = new RepositoryContainer;
		$this->r1 = $this->m1->testentityrepository;
		$this->r2 = $this->m2->testentityrepository;
	}

	public function testNew()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getRepository(false));
		$this->r->attach($e);
		$this->assertSame($this->r, $e->getRepository(false));
	}

	public function testAlreadyAttached()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getRepository());
		$this->r->attach($e);
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testDifferentRC1()
	{
		$e = new TestEntity;

		$e->fireEvent('onAttachModel', NULL, $this->m1);
		$this->setExpectedException('Orm\EntityAlreadyAttachedException', 'TestEntity is already attached to another RepositoryContainer.');
		$e->fireEvent('onAttach', $this->r2);
	}

	public function testDifferentRC2()
	{
		$e = new TestEntity;

		$e->fireEvent('onAttach', $this->r1);
		$this->setExpectedException('Orm\EntityAlreadyAttachedException', 'TestEntity is already attached to another RepositoryContainer.');
		$e->fireEvent('onAttach', $this->r2);
	}

	public function testTwiceOk()
	{
		$e = new TestEntity;
		$e->fireEvent('onAttachModel', NULL, $this->m1);
		$e->fireEvent('onAttach', $this->r1);

		$e = new TestEntity;
		$e->fireEvent('onAttach', $this->r1);
		$e->fireEvent('onAttach', $this->r1);

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
		$r->attach($e);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AttachableEntityFragment', 'onAttach');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
