<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DibiCollection::getMapper
 */
class DibiCollection_getMapper_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\DibiMapper', DibiCollection_DibiCollection::call($this->c, 'getMapper'));
	}

}
