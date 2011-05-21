<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::applyLimit
 */
class DibiCollection_applyLimit_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->c->applyLimit(10, 20);
		$this->assertAttributeSame(10, 'limit', $this->c);
		$this->assertAttributeSame(20, 'offset', $this->c);
	}

	public function testWipe()
	{
		DibiCollection_DibiCollection::set($this->c, 'result', array());
		DibiCollection_DibiCollection::set($this->c, 'count', 666);
		$this->assertAttributeSame(array(), 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);
		$this->c->applyLimit(10, 20);
		$this->assertAttributeSame(NULL, 'result', $this->c);
		$this->assertAttributeSame(NULL, 'count', $this->c);
	}

	public function testReturns()
	{
		$this->assertSame($this->c, $this->c->applyLimit(10, 20));
	}

}
