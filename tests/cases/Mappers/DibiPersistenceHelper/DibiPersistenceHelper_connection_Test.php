<?php

/**
 * @covers Orm\DibiPersistenceHelper::getConnection
 * @covers Orm\DibiPersistenceHelper::setConnection
 */
class DibiPersistenceHelper_connection_Test extends DibiPersistenceHelper_Test
{

	public function testGet()
	{
		$c1 = $this->h->connection;
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($c1, $this->h->conventional, 'table', $this->r->events);

		$this->assertSame($c1, $h->connection);
		$this->assertSame($c1, $h->getConnection());
	}

	public function testSet()
	{
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $this->h->conventional, 'table', $this->r->events);

		$this->setExpectedException('Orm\DeprecatedException', 'Orm\DibiPersistenceHelper::$connection setter is deprecated; use constructor instead');
		$h->connection = 'x';
	}

}
