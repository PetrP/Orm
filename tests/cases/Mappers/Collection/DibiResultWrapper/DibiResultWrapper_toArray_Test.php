<?php

/**
 * @covers Orm\DibiResultWrapper::toArray
 */
class DibiResultWrapper_toArray_Test extends DibiResultWrapper_Base_Test
{

	public function test1()
	{
		$this->assertSame(array($this->repository->getById(1), $this->repository->getById(2)), $this->w->toArray());

		$this->assertSame('fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function test2()
	{
		$this->assertSame($this->repository->getById(2), $this->w->get(1));
		$this->assertSame(NULL, $this->w->get(2));
		$this->assertSame(array($this->repository->getById(1), $this->repository->getById(2)), $this->w->toArray());

		$this->assertSame('seek#1 fetch#1 fetch#2 seek#0 fetch#0', implode(' ', $this->d->operations));
	}

	public function test3()
	{
		$this->assertSame($this->w->toArray(), $this->w->toArray());
		$this->assertSame($this->w->toArray(), $this->w->toArray());
		$this->assertSame($this->repository->getById(2), $this->w->get(1));

		$this->assertSame('fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiResultWrapper', 'toArray');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
