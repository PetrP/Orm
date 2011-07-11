<?php

/**
 * @covers Orm\ArrayCollection::count
 */
class ArrayCollection_count_Test extends ArrayCollection_Base_Test
{

	public function test1()
	{
		$this->assertSame(4, $this->c->count());
	}

	public function test2()
	{
		$this->assertSame(2, $this->c->applyLimit(2)->count());
		$this->assertSame(1, $this->c->applyLimit(1)->count());
	}

}
