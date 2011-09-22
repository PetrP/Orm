<?php

use Orm\HydrateEntityIterator;

/**
 * @covers Orm\HydrateEntityIterator::count
 */
class HydrateEntityIterator_count_Test extends HydrateEntityIterator_Base_Test
{

	public function test()
	{
		$this->d->count = 2;
		$this->assertSame(2, $this->i->count());
	}

	public function testEmpty()
	{
		$this->d->count = 0;
		$this->assertSame(0, $this->i->count());
	}

	public function testAnyTraversableCountable()
	{
		$i = new HydrateEntityIterator($this->r, new ArrayIterator(array(array('id' => 1), array('id' => 2))));
		$this->assertSame(2, $i->count());
	}

	public function testAnyTraversableNotCountable()
	{
		$i = new HydrateEntityIterator($this->r, new IteratorIterator(new ArrayIterator(array(array('id' => 1), array('id' => 2)))));
		$this->assertSame(2, $i->count());
	}

	public function testAnyTraversable()
	{
		$i = new HydrateEntityIterator($this->r, new HydrateEntityIterator_Base_IteratorAggregate(array(array('id' => 1), array('id' => 2))));
		$this->assertSame(2, $i->count());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\HydrateEntityIterator', 'count');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
