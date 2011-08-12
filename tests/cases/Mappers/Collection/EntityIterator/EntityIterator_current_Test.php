<?php

/**
 * @covers Orm\EntityIterator::current
 */
class EntityIterator_current_Test extends EntityIterator_Base_Test
{

	public function test()
	{
		$this->d->count = 2;
		$this->i->next();
		$this->assertSame($this->r->getById(1), $this->i->current());
		$this->i->next();
		$this->assertSame($this->r->getById(2), $this->i->current());
		$this->i->next();
		$this->assertSame(false, $this->i->valid());
	}

	public function testEmpty()
	{
		$this->d->count = 0;
		$this->i->next();
		$this->assertSame(false, $this->i->valid());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EntityIterator', 'current');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
