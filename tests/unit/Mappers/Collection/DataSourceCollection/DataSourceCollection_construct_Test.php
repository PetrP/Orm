<?php

use Orm\DataSourceCollection;

/**
 * @covers Orm\DataSourceCollection::__construct
 * @covers Orm\BaseDibiCollection::__construct
 */
class DataSourceCollection_construct_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->assertAttributeSame('datasourcecollection', 'sql', $this->c);
		$this->assertAttributeSame($this->m->repository, 'repository', $this->c);
		$this->assertAttributeSame($this->m->connection, 'connection', $this->c);
		$this->assertAttributeSame($this->m->repository->mapper->conventional, 'conventional', $this->c);
	}

	public function testSql()
	{
		$this->a('SELECT * FROM `datasourcecollection`');
	}

	public function testAscDesc()
	{
		$this->assertSame(Dibi::DESC, DataSourceCollection::DESC);
		$this->assertSame(Dibi::ASC, DataSourceCollection::ASC);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DataSourceCollection', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
