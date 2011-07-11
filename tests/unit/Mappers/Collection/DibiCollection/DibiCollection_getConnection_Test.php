<?php

/**
 * @covers Orm\DibiCollection::getConnection
 */
class DibiCollection_getConnection_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('DibiConnection', DibiCollection_DibiCollection::call($this->c, 'getConnection'));
	}

}
