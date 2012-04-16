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
		$this->d->addExpected('query', true, "INSERT INTO `dibimapper_connected_dibi` (`string`, `date`) VALUES ('xxx', '2011-11-11 00:00:00')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$e = new TestEntity;
		$e->string = 'xxx';
		$e->date = '2011-11-11';
		$r = $this->m->persist($e);
		$this->assertSame(3, $r);
	}

	public function testUpdate()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('query', true, "SELECT `id` FROM `dibimapper_connected_dibi` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('query', true, "UPDATE `dibimapper_connected_dibi` SET `string`='xxx', `date`='2011-11-11 00:00:00' WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$e = $this->m->repository->hydrateEntity(array('id' => 3));
		$e->string = 'xxx';
		$e->date = '2011-11-11';
		$r = $this->m->persist($e);
		$this->assertSame(3, $r);
	}

	public function testUpdateNotChanged()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('query', true, "SELECT `id` FROM `dibimapper_connected_dibi` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('query', true, "UPDATE `dibimapper_connected_dibi` SET `string`='xxx' WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$e = $this->m->repository->hydrateEntity(array('id' => 3));
		$e->string = 'xxx';
		$r = $this->m->persist($e);
		$this->assertSame(3, $r);

		$this->d->addExpected('query', true, "SELECT `id` FROM `dibimapper_connected_dibi` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('query', true, "UPDATE `dibimapper_connected_dibi` SET `string`='xxx', `date`='2011-11-11 00:00:00' WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$e->date = '2011-11-11';
		$r = $this->m->persist($e);
		$this->assertSame(3, $r);
	}

	public function testUpdateNotInDatabase()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('query', true, "SELECT `id` FROM `dibimapper_connected_dibi` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', NULL, true);
		$this->d->addExpected('query', true, "INSERT INTO `dibimapper_connected_dibi` (`id`, `string`, `date`) VALUES (3, 'xxx', '2011-11-11 00:00:00')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$e = $this->m->repository->hydrateEntity(array('id' => 3));
		$e->string = 'xxx';
		$e->date = '2011-11-11';
		$r = $this->m->persist($e);
		$this->assertSame(3, $r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'persist');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
