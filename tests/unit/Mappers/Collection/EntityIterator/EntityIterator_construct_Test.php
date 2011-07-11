<?php

/**
 * @covers Orm\EntityIterator::__construct
 */
class EntityIterator_construct_Test extends EntityIterator_Base_Test
{
	public function test1()
	{
		$this->assertAttributeSame($this->r, 'repository', $this->i);
	}

	public function test2()
	{
		$this->assertInstanceOf('Traversable', $this->i);
		$this->assertInstanceOf('Countable', $this->i);
	}
}
