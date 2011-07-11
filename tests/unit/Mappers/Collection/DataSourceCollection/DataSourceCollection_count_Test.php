<?php

/**
 * @covers Orm\DataSourceCollection::count
 */
class DataSourceCollection_count_Test extends DataSourceCollection_BaseConnected_Test
{

	public function test()
	{
		$this->d->addExpected('query', true, 'SELECT COUNT(*) FROM `datasourcecollectionconnected`');
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('COUNT(*)' => 3), true);
		$this->assertSame(3, $this->c->count());
		$this->assertSame(3, $this->c->count()); // cache
	}

}
