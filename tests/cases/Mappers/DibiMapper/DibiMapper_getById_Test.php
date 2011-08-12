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
		$this->d->addExpected('query', true, "SELECT `e`.* FROM `dibimapper_connected_dibi` as e WHERE (`id` = '1') LIMIT 1");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('id' => 1), true);
		$e = $this->m->getById(1);
		$this->assertInstanceOf('Orm\IEntity', $e);
		$this->assertSame(1, $e->id);
	}

	public function testPrimaryKey()
	{
		setAccessible(new ReflectionProperty('Orm\Mapper', 'conventional'))
			->setValue($this->m, new SqlConventional_getPrimaryKey_SqlConventional($this->m))
		;
		$this->d->addExpected('query', true, "SELECT `e`.* FROM `dibimapper_connected_dibi` as e WHERE (`foo_bar` = '1') LIMIT 1");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('fetch', array('foo_bar' => 1), true);
		$e = $this->m->getById(1);
		$this->assertInstanceOf('Orm\IEntity', $e);
		$this->assertSame(1, $e->id);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'getById');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
