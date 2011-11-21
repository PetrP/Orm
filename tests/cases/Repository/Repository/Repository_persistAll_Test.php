<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::persistAll
 */
class Repository_persistAll_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->Repository_persist_;
	}

	public function testNone()
	{
		$this->r->persistAll();
		$this->assertSame(0, $this->r->mapper->count);
	}

	public function testNoneHasLoad()
	{
		$this->r->mapper->findAll()->fetchAll();
		$this->r->persistAll();
		$this->assertSame(0, $this->r->mapper->count);
	}

	public function testChanged()
	{
		$es = $this->r->mapper->findAll()->fetchAll();
		$es[0]->markAsChanged();
		$this->r->persistAll();
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(false, $es[0]->isChanged());
	}

	public function testNew()
	{
		$e = $this->r->attach(new TestEntity);
		$this->r->persistAll();
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(3, $e->id);
		$this->assertSame(false, $e->isChanged());
	}

	public function testNewAndChanged()
	{
		$es = $this->r->mapper->findAll()->fetchAll();
		$e = $this->r->attach(new TestEntity);
		$es[0]->markAsChanged();
		$this->r->persistAll();
		$this->assertSame(2, $this->r->mapper->count);
		$this->assertSame(false, $es[0]->isChanged());
		$this->assertSame(3, $e->id);
		$this->assertSame(false, $e->isChanged());
	}

	public function testNewAndRemove()
	{
		$e = $this->r->attach(new TestEntity);
		$this->r->remove($e);
		$this->r->persistAll();
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(true, $e->isChanged());
	}

	public function testChangedAndRemove()
	{
		$es = $this->r->mapper->findAll()->fetchAll();
		$es[0]->markAsChanged();
		$this->r->remove($es[0]);
		$this->r->persistAll();
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(true, $es[0]->isChanged());
	}

	public function testNewChanged()
	{
		$e = $this->r->persist(new TestEntity);
		$this->assertSame(1, $this->r->mapper->count);
		$e->markAsChanged();
		$this->r->persistAll();
		$this->assertSame(2, $this->r->mapper->count);
		$this->assertSame(3, $e->id);
		$this->assertSame(false, $e->isChanged());
	}

	public function testNewNotChanged()
	{
		$e = $this->r->persist(new TestEntity);
		$this->assertSame(1, $this->r->mapper->count);
		$this->r->persistAll();
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(3, $e->id);
		$this->assertSame(false, $e->isChanged());
	}

	public function testReturns()
	{
		$this->assertSame($this->r, $this->r->persistAll());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'persistAll');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
