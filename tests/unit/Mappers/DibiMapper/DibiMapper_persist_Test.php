<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::persist
 * @covers Orm\DibiPersistenceHelper::persist
 */
class DibiMapper_persist_Test extends DibiMapper_Connected_Test
{

	public function testInsert()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'string')), 'dibimapper_connected_dibi');
		$this->d->addExpected('query', true, "INSERT INTO `dibimapper_connected_dibi` (`string`) VALUES ('xxx')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$e = new TestEntity;
		$e->string = 'xxx';
		$r = $this->m->persist($e);
		$this->assertSame(3, $r);
	}

	public function testUpdate()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'string')), 'dibimapper_connected_dibi');
		$this->d->addExpected('query', true, "SELECT `id` FROM `dibimapper_connected_dibi` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('query', true, "UPDATE `dibimapper_connected_dibi` SET `id`=3, `string`='xxx' WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$e = new TestEntity;
		$e->___event($e, 'load', $this->m->repository, array('id' => 3));
		$e->string = 'xxx';
		$r = $this->m->persist($e);
		$this->assertSame(3, $r);
	}

	public function testUpdateNotInDatabase()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'string')), 'dibimapper_connected_dibi');
		$this->d->addExpected('query', true, "SELECT `id` FROM `dibimapper_connected_dibi` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', NULL, true);
		$this->d->addExpected('query', true, "INSERT INTO `dibimapper_connected_dibi` (`id`, `string`) VALUES (3, 'xxx')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$e = new TestEntity;
		$e->___event($e, 'load', $this->m->repository, array('id' => 3));
		$e->string = 'xxx';
		$r = $this->m->persist($e);
		$this->assertSame(3, $r);
	}
}
