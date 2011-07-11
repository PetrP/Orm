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

}
