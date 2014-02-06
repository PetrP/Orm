<?php

/**
 * @covers Orm\DibiResultWrapper::fetchPairs
 */
class DibiResultWrapper_fetchPairs_Test extends DibiResultWrapper_Base_Test
{

	public function test1()
	{
		$this->assertSame(array(1 => 1, 2 => 2), $this->w->fetchPairs('id', 'id'));
		$this->assertSame('fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function testSeek1()
	{
		$this->assertSame(array(1 => 1, 2 => 2), $this->w->fetchPairs('id', 'id'));
		$this->assertSame(array(1, 2), $this->w->fetchPairs(NULL, 'id'));
		$this->assertSame('fetch#0 fetch#1 fetch#2 seek#0 fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function testSeek2()
	{
		$this->w->get(0);
		$this->assertSame(array(1, 2), $this->w->fetchPairs(NULL, 'id'));
		$this->assertSame('fetch#0 seek#0 fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiResultWrapper', 'fetchPairs');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
