<?php

/**
 * @covers Orm\DataSourceCollection::getRepository
 */
class DataSourceCollection_getRepository_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IRepository', DataSourceCollection_DataSourceCollection::call($this->c, 'getRepository'));
	}

}
