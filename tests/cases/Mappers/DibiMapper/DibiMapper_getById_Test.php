<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::getById
 */
class DibiMapper_getById_Test extends DibiMapper_Connected_Test
{

	public function testEmpty()
	{
		$r = $this->m->getById('');
		$this->assertSame(NULL, $r);
	}

	public function test()
	{
		$this->d->addExpected('query', true, "SELECT `e`.* FROM `dibimapper_connected_dibi` as e WHERE (`e`.`id` = '1') LIMIT 1");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 1), true);
		$e = $this->m->getById(1);
		$this->assertInstanceOf('Orm\IEntity', $e);
		$this->assertSame(1, $e->id);
	}
}
