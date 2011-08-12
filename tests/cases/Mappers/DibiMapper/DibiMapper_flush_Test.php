<?php

use Orm\RepositoryContainer;

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
		$p = new ReflectionProperty('Orm\DibiMapper', 'connection');
		setAccessible($p);
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

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'flush');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
