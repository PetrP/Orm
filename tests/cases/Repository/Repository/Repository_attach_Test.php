<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::attach
 */
class Repository_attach_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
	}

	public function testNew()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getRepository(false));
		$this->assertSame($e, $this->r->attach($e));
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testBad1()
	{
		$this->setExpectedException('Orm\InvalidEntityException', "TestsRepository can't work with entity 'Repository_persist_Entity', only with 'TestEntity'");
		$this->r->attach(new Repository_persist_Entity);
	}

	public function testBad2()
	{
		$m = new RepositoryContainer;
		$this->setExpectedException('Orm\InvalidEntityException', "TestEntity#1 is attached to another repository.");
		$this->r->attach($m->tests->getById(1));
	}

	public function testAlreadyAttached()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getRepository());
		$this->assertSame($e, $this->r->attach($e));
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'attach');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
