<?php

/**
 * @covers Orm\DataSourceCollection::fetchAssoc
 */
class DataSourceCollection_fetchAssoc_Test extends DataSourceCollection_BaseConnected_Test
{

	public function test()
	{
		$this->e(3);
		$this->assertSame(array(
			1 => 'boo',
			2 => 'foo',
			3 => 'bar',
		), $this->c->fetchAssoc('id=string'));
	}

}
