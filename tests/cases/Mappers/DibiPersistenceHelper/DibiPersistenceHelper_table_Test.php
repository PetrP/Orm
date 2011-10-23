<?php

/**
 * @covers Orm\DibiPersistenceHelper
 */
class DibiPersistenceHelper_table_Test extends DibiPersistenceHelper_Test
{

	public function testGet()
	{
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $this->h->conventional, 'table', $this->r->events);

		$this->assertSame('table', $h->table);
	}

	public function testSet()
	{
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $this->h->conventional, 'table', $this->r->events);

		$h->table = 'x';
		$this->assertSame('x', $h->table);
	}

}
