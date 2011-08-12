<?php

/**
 * @covers Orm\DataSourceCollection::getConventional
 */
class DataSourceCollection_getConventional_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IConventional', DataSourceCollection_DataSourceCollection::call($this->c, 'getConventional'));
	}

}
