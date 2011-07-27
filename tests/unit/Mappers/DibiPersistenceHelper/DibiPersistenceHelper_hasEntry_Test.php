<?php

/**
 * @covers Orm\DibiPersistenceHelper::hasEntry
 */
class DibiPersistenceHelper_hasEntry_Test extends DibiPersistenceHelper_Test
{

	public function testNoId()
	{
		$r = $this->h->call('hasEntry', array($this->e));
		$this->assertSame(false, $r);
	}

	public function testIdNoDb()
	{
		$this->e->___event($this->e, 'load', $this->r, array('id' => 3));
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', NULL, true);
		$r = $this->h->call('hasEntry', array($this->e));
		$this->assertSame(false, $r);
	}

	public function testIdDb()
	{
		$this->e->___event($this->e, 'load', $this->r, array('id' => 3));
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$r = $this->h->call('hasEntry', array($this->e));
		$this->assertSame(true, $r);
	}

}
