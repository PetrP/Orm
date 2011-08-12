<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::rollback
 */
class DibiMapper_rollback_Test extends DibiMapper_Connected_Test
{

	public function testNoTransaction()
	{
		$this->m->rollback();
		$this->assertTrue(true);
	}

	public function testBase()
	{
		$hash = spl_object_hash($this->m->getConnection());
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();

		$t = $this->readAttribute('Orm\DibiMapper', 'transactions');
		$this->assertTrue($t[$hash]);
		$this->d->addExpected('rollback', NULL, NULL);
		$this->m->rollback();
		$this->assertArrayNotHasKey($hash, $this->readAttribute('Orm\DibiMapper', 'transactions'));

		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();
		$this->m->__begin();
		$t = $this->readAttribute('Orm\DibiMapper', 'transactions');
		$this->assertTrue($t[$hash]);
		$this->d->addExpected('rollback', NULL, NULL);
		$this->m->rollback();
		$this->m->rollback();
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

		$this->d->addExpected('rollback', NULL, NULL);
		$m->rollback();
		$this->d->addExpected('begin', NULL, NULL);
		$m->__begin();

		$this->d->addExpected('rollback', NULL, NULL);
		$this->m->rollback();
	}

	public function testTwoMapperDiferentConnection()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();

		$m = new RepositoryContainer;
		$m = $m->DibiMapper_Connected_Dibi->getMapper();
		$m->connection->driver->addExpected('begin', NULL, NULL);
		$m->__begin();

		$this->d->addExpected('rollback', NULL, NULL);
		$this->m->rollback();

		$m->connection->driver->addExpected('rollback', NULL, NULL);
		$m->rollback();
	}

	public function testAfterFlush()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();
		$this->d->addExpected('commit', NULL, NULL);
		$this->m->flush();
		$this->m->rollback();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'rollback');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
