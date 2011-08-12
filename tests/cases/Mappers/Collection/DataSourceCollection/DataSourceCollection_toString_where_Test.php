<?php

/**
 * @covers Orm\DataSourceCollection::__toString
 * @covers Orm\DataSourceCollection::getDataSource
 */
class DataSourceCollection_toString_where_Test extends DataSourceCollection_Base_Test
{

	public function testSql()
	{
		$this->c->where('1=1');
		$this->a('SELECT * FROM `datasourcecollection` WHERE (1=1)');
		$this->c->where('2=2');
		$this->a('SELECT * FROM `datasourcecollection` WHERE (1=1) AND (2=2)');
		$this->c->where(array('`bb` = `aa`'));
		$this->a('SELECT * FROM `datasourcecollection` WHERE (1=1) AND (2=2) AND (`bb` = `aa`)');
		$this->c->where('%n = %s', 'foo', 'bar');
		$this->a("SELECT * FROM `datasourcecollection` WHERE (1=1) AND (2=2) AND (`bb` = `aa`) AND (`foo` = 'bar')");
	}

	public function testSqlSub()
	{
		$this->c->where('1=1');

		$c = $this->c->toCollection()->where('2=2');

		$this->a('SELECT * FROM `datasourcecollection` WHERE (1=1) AND (2=2)', $c);

		$c->where('%n = %s', 'foo', 'bar');

		$this->a("SELECT * FROM `datasourcecollection` WHERE (1=1) AND (2=2) AND (`foo` = 'bar')", $c);

	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DataSourceCollection', '__toString');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
