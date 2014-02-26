<?php

/**
 * @covers Orm\DibiResultWrapper::get
 * @covers Orm\DibiResultWrapper::resultSeek
 * @covers Orm\DibiResultWrapper::resultFetch
 */
class DibiResultWrapper_get_Test extends DibiResultWrapper_Base_Test
{

	public function test1()
	{
		$this->assertSame($this->repository->getById(1), $this->w->get(0));
		$this->assertSame($this->repository->getById(2), $this->w->get(1));
		$this->assertSame(NULL, $this->w->get(2));
		$this->assertSame('fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function test2()
	{
		$this->w->toArray();
		$this->assertSame($this->repository->getById(1), $this->w->get(0));
		$this->assertSame($this->repository->getById(2), $this->w->get(1));
		$this->assertSame(NULL, $this->w->get(2));
		$this->assertSame('fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function test3()
	{
		$this->assertSame($this->repository->getById(1), $this->w->get(0));
		$this->assertSame($this->repository->getById(2), $this->w->get(1));
		$this->assertSame(NULL, $this->w->get(2));

		$this->assertSame($this->repository->getById(1), $this->w->get(0));
		$this->assertSame($this->repository->getById(2), $this->w->get(1));
		$this->assertSame(NULL, $this->w->get(2));

		$this->assertSame($this->repository->getById(1), $this->w->get(0));
		$this->assertSame(NULL, $this->w->get(2));
		$this->assertSame($this->repository->getById(2), $this->w->get(1));

		$this->assertSame('fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function test4()
	{
		$this->assertSame($this->repository->getById(2), $this->w->get(1));
		$this->assertSame(NULL, $this->w->get(5));
		$this->assertSame($this->repository->getById(1), $this->w->get(0));
		$this->assertSame(NULL, $this->w->get(5));

		$this->assertSame('seek#1 fetch#1 seek#5 seek#0 fetch#0', implode(' ', $this->d->operations));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiResultWrapper', 'get');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function testReflectionResultSeek()
	{
		$r = new ReflectionMethod('Orm\DibiResultWrapper', 'resultSeek');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

	public function testReflectionResultFetch()
	{
		$r = new ReflectionMethod('Orm\DibiResultWrapper', 'resultFetch');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
