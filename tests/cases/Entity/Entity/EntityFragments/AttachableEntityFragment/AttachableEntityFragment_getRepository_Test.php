<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::getRepository
 */
class AttachableEntityFragment_getRepository_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentityrepository;
	}

	public function testNotNeed()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getRepository(false));
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getRepository(false));
	}

	public function testNeed1()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getRepository(true));
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testNeed2()
	{
		$e = new TestEntity;
		$this->setExpectedException('Orm\EntityNotAttachedException', 'TestEntity is not attached to repository.');
		$e->getRepository(true);
	}

	public function testAfterRemove()
	{
		$e = $this->r->getById(1);
		$this->r->remove($e);
		$this->assertSame(NULL, $e->getRepository(false));

		$this->setExpectedException('Orm\EntityNotAttachedException', 'TestEntity is not attached to repository.');
		$e->getRepository();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\AttachableEntityFragment', 'getRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
