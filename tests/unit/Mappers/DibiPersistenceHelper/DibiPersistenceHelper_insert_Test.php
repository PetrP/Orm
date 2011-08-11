<?php

/**
 * @covers Orm\DibiPersistenceHelper::insert
 */
class DibiPersistenceHelper_insert_Test extends DibiPersistenceHelper_Test
{

	public function testIdOk()
	{
		$this->d->addExpected('query', true, "INSERT INTO `table` (`aaa`) VALUES ('aaa')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$r = $this->h->call('insert', array(array('aaa' => 'aaa')));
		$this->assertSame(3, $r);
	}

	public function testIdFail()
	{
		$this->d->addExpected('query', true, "INSERT INTO `table` (`aaa`) VALUES ('aaa')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', NULL, NULL);
		$this->setExpectedException('DibiException', 'Cannot retrieve last generated ID.');
		$this->h->call('insert', array(array('aaa' => 'aaa')));
	}

	public function testIdFailHasInArray()
	{
		$this->d->addExpected('query', true, "INSERT INTO `table` (`id`, `aaa`) VALUES (3, 'aaa')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', NULL, NULL);
		$r = $this->h->call('insert', array(array('id' => 3, 'aaa' => 'aaa')));
		$this->assertSame(3, $r);
	}


	public function testIdOkPrimaryKey()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->d->addExpected('query', true, "INSERT INTO `table` (`aaa`) VALUES ('aaa')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', 3, NULL);
		$r = $this->h->call('insert', array(array('aaa' => 'aaa')));
		$this->assertSame(3, $r);
	}

	public function testIdFailPrimaryKey()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->d->addExpected('query', true, "INSERT INTO `table` (`id`, `aaa`) VALUES (3, 'aaa')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', NULL, NULL);
		$this->setExpectedException('DibiException', 'Cannot retrieve last generated ID.');
		$this->h->call('insert', array(array('id' => 3, 'aaa' => 'aaa')));
	}

	public function testIdFailHasInArrayPrimaryKey()
	{
		$this->h->primaryKey = 'foo_bar';
		$this->d->addExpected('query', true, "INSERT INTO `table` (`foo_bar`, `aaa`) VALUES (3, 'aaa')");
		$this->d->addExpected('createResultDriver', NULL, true);
		$this->d->addExpected('getInsertId', NULL, NULL);
		$r = $this->h->call('insert', array(array('foo_bar' => 3, 'aaa' => 'aaa')));
		$this->assertSame(3, $r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiPersistenceHelper', 'insert');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
