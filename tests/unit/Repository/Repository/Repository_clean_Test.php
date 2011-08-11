<?php

/**
 * @covers Orm\Repository::clean
 */
class Repository_clean_Test extends TestCase
{
	private $m;
	private $r;
	private $r2;

	protected function setUp()
	{
		$this->m = new Repository_clean_Model;
		$this->r = $this->m->Repository_clean_;
		$this->r2 = $this->m->Repository_clean2_;
	}

	public function test()
	{
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->r->clean();
		$this->assertSame(1, $this->m->count);
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(1, $this->r2->mapper->count);
	}

	public function test2()
	{
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->r->clean(true);
		$this->assertSame(0, $this->m->count);
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
	}

	public function testNotHandleByModel()
	{
		$r = new Repository_clean_Repository($this->m);
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->assertSame(0, $r->mapper->count);
		$r->clean();
		$this->assertSame(1, $this->m->count);
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(1, $this->r2->mapper->count);
		$this->assertSame(0, $r->mapper->count); // bug?
	}

	public function testNotHandleByModel2()
	{
		$r = new Repository_clean_Repository($this->m);
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->assertSame(0, $r->mapper->count);
		$r->clean(true);
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->assertSame(1, $r->mapper->count);
	}

	public function testClearAllEntity()
	{
		$e = new TestEntity;
		$e->string = 'xyz';
		$this->r->persist($e);
		$this->r->clean();
		$this->assertSame('xyz', $e->string);
		$this->assertTrue(isset($e->id)); // bug?
		$this->assertSame($e, $this->r->getById(3)); // bug?
	}

	public function testClearAllEntity2()
	{
		$e = $this->r->getById(1);
		$e->string = 'xyz';
		$this->r->persist($e);
		$this->r->clean();
		$this->assertSame(1, $e->id);
		$this->assertSame($e, $this->r->getById(1));
		$this->assertSame('xyz', $e->string); // bug?
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'clean');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
