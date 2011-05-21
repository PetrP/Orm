<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\ArrayCollection::getTotalCount
 */
class ArrayCollection_getTotalCount_Test extends ArrayCollection_Base_Test
{

	public function test1()
	{
		$this->assertSame(4, $this->c->getTotalCount());
	}

	public function test2()
	{
		$this->assertSame(4, $this->c->applyLimit(1)->getTotalCount());
	}

}
