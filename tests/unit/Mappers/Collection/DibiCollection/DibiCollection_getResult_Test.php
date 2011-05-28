<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::getResult
 */
class DibiCollection_getResult_Test extends DibiCollection_BaseConnected_Test
{

	public function test()
	{
		$this->e(0, false);
		$r = $this->c->getResult();
		$this->assertInstanceOf('DibiResult', $r);
		$this->assertSame($r, $this->c->getResult());
	}

	public function testCache()
	{
		$this->e(0, false);
		$this->assertSame($this->c->getResult(), $this->c->getResult());
	}
}
