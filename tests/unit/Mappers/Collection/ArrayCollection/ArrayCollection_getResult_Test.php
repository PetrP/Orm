<?php

/**
 * @covers Orm\ArrayCollection::getResult
 */
class ArrayCollection_getResult_Test extends ArrayCollection_Base_Test
{

	public function testReturns()
	{
		$this->assertInternalType('array', $this->c->getResult());
	}

	public function test()
	{
		$this->assertSame($this->e, $this->c->getResult());
	}

}
