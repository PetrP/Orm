<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::remove
 */
class DibiMapper_remove_Test extends DibiMapper_Connected_Test
{

	public function test()
	{
		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('query', true, "DELETE FROM `dibimapper_connected_dibi` WHERE `id` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$e = new TestEntity;
		$e->___event($e, 'load', $this->m->repository, array('id' => 3));
		$r = $this->m->remove($e);
		$this->assertSame(true, $r);
	}

	public function testPrimaryKey()
	{
		setAccessible(new ReflectionProperty('Orm\Mapper', 'conventional'))
			->setValue($this->m, new SqlConventional_getPrimaryKey_SqlConventional($this->m))
		;
		$this->d->addExpected('begin', NULL, NULL);
		$this->d->addExpected('query', true, "DELETE FROM `dibimapper_connected_dibi` WHERE `foo_bar` = '3'");
		$this->d->addExpected('createResultDriver', NULL, true);
		$e = new TestEntity;
		$e->___event($e, 'load', $this->m->repository, array('id' => 3));
		$r = $this->m->remove($e);
		$this->assertSame(true, $r);
	}
}
