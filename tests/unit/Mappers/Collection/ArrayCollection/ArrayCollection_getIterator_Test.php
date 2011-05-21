<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\ArrayCollection::getIterator
 */
class ArrayCollection_getIterator_Test extends ArrayCollection_Base_Test
{

	public function test1()
	{
		$this->assertInstanceOf('ArrayIterator', $this->c->getIterator());
	}

	public function test2()
	{
		$this->assertSame($this->e, iterator_to_array($this->c->getIterator()));
	}

}
