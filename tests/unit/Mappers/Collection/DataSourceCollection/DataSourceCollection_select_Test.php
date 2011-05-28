<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\DataSourceCollection::select
 */
class DataSourceCollection_select_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->setExpectedException('Nette\DeprecatedException', 'DataSourceCollection::select() is deprecated; use DataSourceCollection->getDataSource()->select() instead');
		$this->c->select('foo');
	}

}
