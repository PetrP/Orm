<?php

/**
 * @covers Orm\DibiCollection::toDataSourceCollection
 */
class DibiCollection_toDataSourceCollection_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$c = $this->c->toDataSourceCollection();
		$this->assertInstanceOf('Orm\DataSourceCollection', $c);
		$this->assertAttributeSame($this->readAttribute($this->c, 'repository'), 'repository', $c);
		$this->assertAttributeSame($this->readAttribute($this->c, 'connection'), 'connection', $c);
		$this->assertSame('SELECT `e`.* FROM `dibicollection` as e', trim(preg_replace('#\s+#', ' ', $this->readAttribute($c, 'sql'))));
	}

}
