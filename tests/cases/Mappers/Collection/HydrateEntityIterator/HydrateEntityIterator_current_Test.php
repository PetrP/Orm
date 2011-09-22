<?php

use Orm\HydrateEntityIterator;

/**
 * @covers Orm\HydrateEntityIterator::current
 */
class HydrateEntityIterator_current_Test extends HydrateEntityIterator_Base_Test
{

	public function test()
	{
		$this->d->count = 2;
		$this->i->rewind();
		$this->assertSame(true, $this->i->valid());
		$this->assertSame($this->r->getById(1), $this->i->current());
		$this->i->next();
		$this->assertSame(true, $this->i->valid());
		$this->assertSame($this->r->getById(2), $this->i->current());
		$this->i->next();
		$this->assertSame(false, $this->i->valid());
	}

	public function testEmpty()
	{
		$this->d->count = 0;
		$this->i->rewind();
		$this->assertSame(false, $this->i->valid());
	}

	public function testAnyTraversable()
	{
		$i = new HydrateEntityIterator($this->r, new ArrayIterator(array(array('id' => 1), array('id' => 2))));
		$i->rewind();
		$this->assertSame(true, $i->valid());
		$this->assertSame($this->r->getById(1), $i->current());
		$i->next();
		$this->assertSame(true, $i->valid());
		$this->assertSame($this->r->getById(2), $i->current());
		$i->next();
		$this->assertSame(false, $i->valid());
	}

	public function testAnyTraversable2()
	{
		$i = new HydrateEntityIterator($this->r, new IteratorIterator(new ArrayIterator(array(array('id' => 1), array('id' => 2)))));
		$i->rewind();
		$this->assertSame(true, $i->valid());
		$this->assertSame($this->r->getById(1), $i->current());
		$i->next();
		$this->assertSame(true, $i->valid());
		$this->assertSame($this->r->getById(2), $i->current());
		$i->next();
		$this->assertSame(false, $i->valid());
	}

	public function testAnyTraversable3()
	{
		$i = new HydrateEntityIterator($this->r, new HydrateEntityIterator_Base_IteratorAggregate(array(array('id' => 1), array('id' => 2))));
		$i->rewind();
		$this->assertSame(true, $i->valid());
		$this->assertSame($this->r->getById(1), $i->current());
		$i->next();
		$this->assertSame(true, $i->valid());
		$this->assertSame($this->r->getById(2), $i->current());
		$i->next();
		$this->assertSame(false, $i->valid());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\HydrateEntityIterator', 'current');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
