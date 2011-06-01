<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\DibiMapper::flush
 */
class DibiMapper_flush_Test extends DibiMapper_Connected_Test
{

	public function testNoTransaction()
	{
		$this->m->flush();
		$this->assertTrue(true);
	}

	public function testBase()
	{
		$hash = spl_object_hash($this->m->getConnection());
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();

		$t = $this->readAttribute('Orm\DibiMapper', 'transactions');
		$this->assertTrue($t[$hash]);
		$this->d->addExpected('commit', NULL, NULL);
		$this->m->flush();
		$this->assertArrayNotHasKey($hash, $this->readAttribute('Orm\DibiMapper', 'transactions'));

		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();
		$this->m->__begin();
		$t = $this->readAttribute('Orm\DibiMapper', 'transactions');
		$this->assertTrue($t[$hash]);
		$this->d->addExpected('commit', NULL, NULL);
		$this->m->flush();
		$this->m->flush();
		$this->assertArrayNotHasKey($hash, $this->readAttribute('Orm\DibiMapper', 'transactions'));
	}

	public function testTwoMapperSameConnection()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();

		$m = new RepositoryContainer;
		$m = $m->DibiMapper_Connected_Dibi->getMapper();
		if (PHP_VERSION_ID < 50300)
		{
			throw new PHPUnit_Framework_IncompleteTestError('php 5.2 (setAccessible)');
		}
		$p = new ReflectionProperty('Orm\DibiMapper', 'connection');
		$p->setAccessible(true);
		$p->setValue($m, $this->m->getConnection());

		$this->d->addExpected('commit', NULL, NULL);
		$m->flush();
		$this->d->addExpected('begin', NULL, NULL);
		$m->__begin();

		$this->d->addExpected('commit', NULL, NULL);
		$this->m->flush();
	}

	public function testTwoMapperDiferentConnection()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();

		$m = new RepositoryContainer;
		$m = $m->DibiMapper_Connected_Dibi->getMapper();
		$m->connection->driver->addExpected('begin', NULL, NULL);
		$m->__begin();

		$this->d->addExpected('commit', NULL, NULL);
		$this->m->flush();

		$m->connection->driver->addExpected('commit', NULL, NULL);
		$m->flush();
	}

	public function testAfterRollback()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();
		$this->d->addExpected('rollback', NULL, NULL);
		$this->m->rollback();
		$this->m->flush();
	}

}
