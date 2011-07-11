<?php

use Orm\DibiPersistenceHelper;

/**
 * @covers Orm\DibiPersistenceHelper
 */
class DibiMapper_persist3_Test extends DibiMapper_Connected_Test
{

	private $e;
	private $h;
	protected function setUp()
	{
		parent::setUp();
		$this->h = new DibiPersistenceHelper;
		$this->h->table = 'table';
		$this->h->connection = $this->m->connection;
		$this->h->conventional = $this->m->conventional;
		$this->h->mapper = $this->m;
		$this->e = new DibiMapper_persist_Entity;
		$this->e->mixed = 1;
		$this->e->mixed2 = 2;
		$this->e->mixed3 = 3;
	}

	public function testNormal()
	{
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed'), array('name' => 'mixed2'), array('name' => 'mixed3')), 'table');
		$this->d->addExpected('query', true, "INSERT INTO `table` (`mixed`, `mixed2`, `mixed3`) VALUES (1, 2, 3)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWitchParams()
	{
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed'), array('name' => 'mixed2'), array('name' => 'mixed3')), 'table');
		$this->d->addExpected('query', true, "INSERT INTO `table` (`mixed`, `mixed3`) VALUES (1, 3)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$this->h->witchParams = array('mixed', 'mixed3');
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWitchParamsNot()
	{
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed'), array('name' => 'mixed2'), array('name' => 'mixed3')), 'table');
		$this->d->addExpected('query', true, "INSERT INTO `table` (`mixed2`) VALUES (2)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$this->h->witchParamsNot = array('mixed', 'mixed3');
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWitchParamsAndWitchParamsNot()
	{
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed'), array('name' => 'mixed2'), array('name' => 'mixed3')), 'table');
		$this->d->addExpected('query', true, "INSERT INTO `table` (`mixed2`) VALUES (2)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$this->h->witchParams = array('mixed', 'mixed2');
		$this->h->witchParamsNot = array('mixed');
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWitchParamsId()
	{
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed'), array('name' => 'mixed2'), array('name' => 'mixed3')), 'table');
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('query', true, "UPDATE `table` SET `id`=3 WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->h->witchParams = array('id');
		$this->e->___event($this->e, 'load', $this->m->repository, array('id' => 3));
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWitchParamsNotId()
	{
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed'), array('name' => 'mixed2'), array('name' => 'mixed3')), 'table');
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('query', true, "UPDATE `table` SET `id`=3, `mixed`=NULL, `mixed2`=NULL, `mixed3`=NULL WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->h->witchParamsNot = array('id');
		$this->e->___event($this->e, 'load', $this->m->repository, array('id' => 3));
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testId()
	{
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed'), array('name' => 'mixed2'), array('name' => 'mixed3')), 'table');
		$this->d->addExpected('query', true, "INSERT INTO `table` (`id`, `mixed`, `mixed2`, `mixed3`) VALUES (666, 1, 2, 3)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', NULL, NULL);
		$r = $this->h->persist($this->e, 666);
		$this->assertSame(666, $r);
	}

	public function testIdAndGetInsertIdFail()
	{
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed'), array('name' => 'mixed2'), array('name' => 'mixed3')), 'table');
		$this->d->addExpected('query', true, "INSERT INTO `table` (`mixed`, `mixed2`, `mixed3`) VALUES (1, 2, 3)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', NULL, NULL);
		$this->setExpectedException('DibiException', 'Cannot retrieve last generated ID.');
		$this->h->persist($this->e);
	}

	public function testNotInDb()
	{
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed'), array('name' => 'mixed2'), array('name' => 'mixed3')), 'table');
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '666'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', NULL, true);
		$this->d->addExpected('query', true, "INSERT INTO `table` (`id`, `mixed`, `mixed2`, `mixed3`) VALUES (666, NULL, NULL, NULL)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 666, NULL);
		$this->e->___event($this->e, 'load', $this->m->repository, array('id' => 666));
		$r = $this->h->persist($this->e);
		$this->assertSame(666, $r);
	}

	public function testNotInDbAndGetInsertIdFail()
	{
		$this->d->addExpected('getColumns', array(array('name' => 'id'), array('name' => 'mixed'), array('name' => 'mixed2'), array('name' => 'mixed3')), 'table');
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '666'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', NULL, true);
		$this->d->addExpected('query', true, "INSERT INTO `table` (`id`, `mixed`, `mixed2`, `mixed3`) VALUES (666, NULL, NULL, NULL)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', NULL, NULL);
		$this->e->___event($this->e, 'load', $this->m->repository, array('id' => 666));
		$r = $this->h->persist($this->e);
		$this->assertSame(666, $r);
	}


}
