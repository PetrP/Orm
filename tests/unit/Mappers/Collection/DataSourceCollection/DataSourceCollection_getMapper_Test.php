<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DataSourceCollection::getMapper
 */
class DataSourceCollection_getMapper_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\DibiMapper', DataSourceCollection_DataSourceCollection::call($this->c, 'getMapper'));
	}

}
