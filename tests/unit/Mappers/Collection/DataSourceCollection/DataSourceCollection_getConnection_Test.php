<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DataSourceCollection::getConnection
 */
class DataSourceCollection_getConnection_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('DibiConnection', DataSourceCollection_DataSourceCollection::call($this->c, 'getConnection'));
	}

}
