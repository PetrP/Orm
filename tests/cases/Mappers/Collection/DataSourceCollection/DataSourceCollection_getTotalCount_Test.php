<?php

/**
 * @covers Orm\DataSourceCollection::getTotalCount
 */
class DataSourceCollection_getTotalCount_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->setExpectedException('Nette\NotImplementedException');
		$this->c->getTotalCount();
	}

}
