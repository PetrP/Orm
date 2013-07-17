<?php

use Orm\DibiResultWrapperIterator;

/**
 * @covers Orm\DibiResultWrapperIterator
 */
class DibiResultWrapperIterator_Test extends DibiResultWrapper_Base_Test
{

	private $i;
	protected function setUp()
	{
		parent::setUp();
		$this->i = new DibiResultWrapperIterator($this->w);
	}

	public function test()
	{
		$this->assertInstanceOf('Iterator', $this->i);
		$this->assertInstanceOf('Countable', $this->i);
	}

	public function testIterate()
	{
		$a = iterator_to_array($this->i);
		$this->assertSame(array($this->repository->getById(1), $this->repository->getById(2)), $a);
		$this->assertSame($a, iterator_to_array($this->i));
		$this->assertSame($a, iterator_to_array($this->i));
		$this->assertSame('fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function testIterate2()
	{
		$this->assertSame($this->w->toArray(), iterator_to_array($this->i));
		$this->assertSame('fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function testIterate3()
	{
		$a = array();
		foreach ($this->i as $key => $e)
		{
			$a[] = array($key, $e);
		}
		$this->assertSame(array(array(0, $this->repository->getById(1)), array(1, $this->repository->getById(2))), $a);
		$this->assertSame('fetch#0 fetch#1 fetch#2', implode(' ', $this->d->operations));
	}

	public function testCount()
	{
		$this->assertSame(2, $this->i->count());
		$this->assertSame(2, count($this->i));
		$this->assertSame(2, iterator_count($this->i));
	}

	public function testReflection()
	{
		foreach (array('__construct', 'rewind', 'key', 'current', 'next', 'valid', 'count') as $method)
		{
			$r = new ReflectionMethod('Orm\DibiResultWrapperIterator', $method);
			$this->assertTrue($r->isPublic(), 'visibility');
			$this->assertFalse($r->isFinal(), 'final');
			$this->assertFalse($r->isStatic(), 'static');
			$this->assertFalse($r->isAbstract(), 'abstract');
		}
	}

}
