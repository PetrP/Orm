<?php

/**
 * @covers Orm\DibiPersistenceHelper::getEvents
 */
class DibiPersistenceHelper_getEvents_Test extends DibiPersistenceHelper_Test
{

	public function test()
	{
		$c2 = $this->h->conventional;
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $c2, 'table', $this->r->events);

		$this->assertSame($this->r->events, $h->events);
		$this->assertSame($this->r->events, $h->getEvents());
	}

}
