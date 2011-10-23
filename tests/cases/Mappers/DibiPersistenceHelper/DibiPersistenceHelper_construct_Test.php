<?php

/**
 * @covers Orm\DibiPersistenceHelper::__construct
 */
class DibiPersistenceHelper_construct_Test extends DibiPersistenceHelper_Test
{

	public function test()
	{
		$c1 = $this->h->connection;
		$c2 = $this->h->conventional;
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($c1, $c2, 'table', $this->r->events);

		$this->assertSame($c1, $h->connection);
		$this->assertSame($c2, $h->conventional);
		$this->assertSame('table', $h->table);
		$this->assertSame('id', $h->primaryKey);
		$this->assertSame($this->r->events, $h->events);
	}

	public function testPrimaryKey()
	{
		$c2 = new SqlConventional_getPrimaryKey_SqlConventional;
		$h = new DibiPersistenceHelper_DibiPersistenceHelper($this->h->connection, $c2, 'table', $this->r->events);
		$this->assertSame('foo_bar', $h->primaryKey);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiPersistenceHelper', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
