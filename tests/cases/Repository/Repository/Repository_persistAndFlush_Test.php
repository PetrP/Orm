<?php

/**
 * @covers Orm\Repository::persistAndFlush
 */
class Repository_persistAndFlush_Test extends TestCase
{
	private $m;
	private $pr;
	private $fr;
	private $fr2;

	protected function setUp()
	{
		$this->m = new Repository_flush_Model;
		$this->pr = $this->m->Repository_persist_;
		$this->fr = $this->m->Repository_flush_;
		$this->fr2 = $this->m->Repository_flush2_;
	}

	public function testPersist()
	{
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->pr->mapper->count);
		$e = new TestEntity;
		$this->assertSame($e, $this->pr->persistAndFlush($e));
		$this->assertSame(3, $e->id);
		$this->assertSame($e, $this->pr->getById(3));
		$this->assertSame(1, $this->m->count);
		$this->assertSame(1, $this->pr->mapper->count);
	}

	public function testFlush()
	{
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->fr->mapper->count);
		$this->assertSame(0, $this->fr2->mapper->count);
		$this->fr->persistAndFlush(new TestEntity);
		$this->assertSame(1, $this->m->count);
		$this->assertSame(1, $this->fr->mapper->count);
		$this->assertSame(1, $this->fr2->mapper->count);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'persistAndFlush');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
