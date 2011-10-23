<?php

/**
 * @covers Orm\DibiPersistenceHelper
 */
class DibiPersistenceHelper_primaryKey_Test extends DibiPersistenceHelper_Test
{

	public function testGet()
	{
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $this->h->conventional, 'table', $this->r->events);

		$this->assertSame('id', $h->primaryKey);
	}

	public function testSet()
	{
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $this->h->conventional, 'table', $this->r->events);

		$h->primaryKey = 'x';
		$this->assertSame('x', $h->primaryKey);
	}

}
