<?php

/**
 * @covers Orm\DibiPersistenceHelper::getConventional
 * @covers Orm\DibiPersistenceHelper::setConventional
 */
class DibiPersistenceHelper_conventional_Test extends DibiPersistenceHelper_Test
{

	public function testGet()
	{
		$c2 = $this->h->conventional;
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $c2, 'table', $this->r->events);

		$this->assertSame($c2, $h->conventional);
		$this->assertSame($c2, $h->getConventional());
	}

	public function testSet()
	{
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $this->h->conventional, 'table', $this->r->events);

		$this->setExpectedException('Orm\DeprecatedException', 'Orm\DibiPersistenceHelper::$conventional setter is deprecated; use constructor instead');
		$h->conventional = 'x';
	}

}
