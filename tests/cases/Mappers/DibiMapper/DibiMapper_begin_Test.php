<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::begin
 */
class DibiMapper_begin_Test extends DibiMapper_Connected_Test
{

	public function testBase()
	{
		$hash = spl_object_hash($this->m->getConnection());
		$this->assertArrayNotHasKey($hash, $this->readAttribute('Orm\DibiMapper', 'transactions'));
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();
		$t = $this->readAttribute('Orm\DibiMapper', 'transactions');
		$this->assertTrue($t[$hash]);
		$this->m->__begin();
		$this->m->__begin();
		$t = $this->readAttribute('Orm\DibiMapper', 'transactions');
		$this->assertTrue($t[$hash]);
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

		$m->__begin();
	}

	public function testTwoMapperDiferentConnection()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();

		$m = new RepositoryContainer;
		$m = $m->DibiMapper_Connected_Dibi->getMapper();
		$m->connection->driver->addExpected('begin', NULL, NULL);
		$m->__begin();
	}

	public function testAfterRollback()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();
		$this->m->__begin();
		$this->d->addExpected('rollback', NULL, NULL);
		$this->m->rollback();
		$this->d->addExpected('begin', NULL, NULL);
		$this->m->__begin();
		$this->m->__begin();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'begin');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
