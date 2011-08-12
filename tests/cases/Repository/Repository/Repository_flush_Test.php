<?php

/**
 * @covers Orm\Repository::flush
 */
class Repository_flush_Test extends TestCase
{
	private $m;
	private $r;
	private $r2;

	protected function setUp()
	{
		$this->m = new Repository_flush_Model;
		$this->r = $this->m->Repository_flush_;
		$this->r2 = $this->m->Repository_flush2_;
	}

	public function test()
	{
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->r->flush();
		$this->assertSame(1, $this->m->count);
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(1, $this->r2->mapper->count);
	}

	public function test2()
	{
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->r->flush(true);
		$this->assertSame(0, $this->m->count);
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
	}

	public function testNotHandleByModel()
	{
		$r = new Repository_flush_Repository($this->m);
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->assertSame(0, $r->mapper->count);
		$r->flush();
		$this->assertSame(1, $this->m->count);
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(1, $this->r2->mapper->count);
		$this->assertSame(0, $r->mapper->count); // bug?
	}

	public function testNotHandleByModel2()
	{
		$r = new Repository_flush_Repository($this->m);
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->assertSame(0, $r->mapper->count);
		$r->flush(true);
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->assertSame(1, $r->mapper->count);
	}

}
