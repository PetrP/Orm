<?php

/**
 * @covers Orm\DibiCollection::getConventional
 */
class DibiCollection_getConventional_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IConventional', DibiCollection_DibiCollection::call($this->c, 'getConventional'));
	}

}
