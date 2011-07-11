<?php

/**
 * @covers Orm\DataSourceCollection::join
 */
class DataSourceCollection_join_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->setExpectedException('Nette\NotSupportedException', 'Joins are not supported for DataSourceCollection');
		$this->c->join('foo->bar');
	}

}
