<?php

/**
 * @covers Orm\DibiPersistenceHelper::__construct
 */
class DibiPersistenceHelper_construct_Test extends DibiPersistenceHelper_Test
{

	public function test()
	{
		$c1 = $this->h->connection;
		$c2 = $this->h->conventional;
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($c1, $c2, 'table');

		$this->assertSame($c1, $h->connection);
		$this->assertSame($c2, $h->conventional);
		$this->assertSame('table', $h->table);
		$this->assertSame('id', $h->primaryKey);
	}

	public function testPrimaryKey()
	{
		$c2 = new SqlConventional_getPrimaryKey_SqlConventional;
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $c2, 'table');
		$this->assertSame('foo_bar', $h->primaryKey);
	}

}
