<?php

use Orm\DibiPersistenceHelper;

/**
 * @covers Orm\DibiPersistenceHelper::persist
 */
class DibiMapper_persist3_Test extends DibiMapper_Connected_Test
{

	private $e;
	private $h;
	protected function setUp()
	{
		parent::setUp();
		$this->h = new DibiPersistenceHelper($this->m->connection, $this->m->conventional, 'table', $this->m->repository->events);
		$this->e = new DibiMapper_persist_Entity;
		$this->e->mixed = 1;
		$this->e->mixed2 = 2;
		$this->e->mixed3 = 3;
	}

	public function testNormal()
	{
		$this->d->addExpected('query', true, "INSERT INTO `table` (`mixed`, `mixed2`, `mixed3`) VALUES (1, 2, 3)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWhichParams()
	{
		$this->d->addExpected('query', true, "INSERT INTO `table` (`mixed`, `mixed3`) VALUES (1, 3)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$this->h->whichParams = array('mixed', 'mixed3');
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWhichParamsNot()
	{
		$this->d->addExpected('query', true, "INSERT INTO `table` (`mixed2`) VALUES (2)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$this->h->whichParamsNot = array('mixed', 'mixed3');
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWhichParamsAndWhichParamsNot()
	{
		$this->d->addExpected('query', true, "INSERT INTO `table` (`mixed2`) VALUES (2)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$this->h->whichParams = array('mixed', 'mixed2');
		$this->h->whichParamsNot = array('mixed');
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWhichParamsId()
	{
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('query', true, "UPDATE `table` SET `id`=3 WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->h->whichParams = array('id');
		$this->e->fireEvent('onLoad', $this->m->repository, array('id' => 3));
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWhichParamsIdNot()
	{
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('query', true, "UPDATE `table` SET `id`=3 WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->h->whichParams = array();
		$this->e->fireEvent('onLoad', $this->m->repository, array('id' => 3));
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testWhichParamsNotId()
	{
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 3), true);
		$this->d->addExpected('query', true, "UPDATE `table` SET `id`=3, `mixed`=NULL, `mixed2`=NULL, `mixed3`=NULL WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->h->whichParamsNot = array('id');
		$this->e->fireEvent('onLoad', $this->m->repository, array('id' => 3));
		$r = $this->h->persist($this->e);
		$this->assertSame(3, $r);
	}

	public function testId()
	{
		$this->d->addExpected('query', true, "INSERT INTO `table` (`id`, `mixed`, `mixed2`, `mixed3`) VALUES (666, 1, 2, 3)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', NULL, NULL);
		$r = $this->h->persist($this->e, 666);
		$this->assertSame(666, $r);
	}

	public function testIdAndGetInsertIdFail()
	{
		$this->d->addExpected('query', true, "INSERT INTO `table` (`mixed`, `mixed2`, `mixed3`) VALUES (1, 2, 3)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', NULL, NULL);
		$this->setExpectedException('DibiException', 'Cannot retrieve last generated ID.');
		$this->h->persist($this->e);
	}

	public function testNotInDb()
	{
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '666'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', NULL, true);
		$this->d->addExpected('query', true, "INSERT INTO `table` (`id`, `mixed`, `mixed2`, `mixed3`) VALUES (666, NULL, NULL, NULL)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 666, NULL);
		$this->e->fireEvent('onLoad', $this->m->repository, array('id' => 666));
		$r = $this->h->persist($this->e);
		$this->assertSame(666, $r);
	}

	public function testNotInDbAndGetInsertIdFail()
	{
		$this->d->addExpected('query', true, "SELECT `id` FROM `table` WHERE `id` = '666'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', NULL, true);
		$this->d->addExpected('query', true, "INSERT INTO `table` (`id`, `mixed`, `mixed2`, `mixed3`) VALUES (666, NULL, NULL, NULL)");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', NULL, NULL);
		$this->e->fireEvent('onLoad', $this->m->repository, array('id' => 666));
		$r = $this->h->persist($this->e);
		$this->assertSame(666, $r);
	}


}
