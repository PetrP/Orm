<?php

/**
 * @covers Orm\EntityIterator::count
 */
class EntityIterator_count_Test extends EntityIterator_Base_Test
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

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EntityIterator', 'count');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
