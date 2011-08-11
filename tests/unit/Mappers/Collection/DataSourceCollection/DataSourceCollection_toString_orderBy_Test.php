<?php

/**
 * @covers Orm\DataSourceCollection::process
 * @covers Orm\DataSourceCollection::__toString
 * @covers Orm\DataSourceCollection::getDataSource
 */
class DataSourceCollection_toString_orderBy_Test extends DataSourceCollection_Base_Test
{

	public function testSql()
	{
		$this->c->orderBy('aaa');
		$this->a('SELECT * FROM `datasourcecollection` ORDER BY `aaa` ASC');
		$this->c->orderBy('bbb', Dibi::DESC);
		$this->a('SELECT * FROM `datasourcecollection` ORDER BY `aaa` ASC, `bbb` DESC');
		$this->c->orderBy(array());
		$this->a('SELECT * FROM `datasourcecollection`');
	}

	public function testTwoSame()
	{
		$this->c->orderBy('aaa');
		$this->a('SELECT * FROM `datasourcecollection` ORDER BY `aaa` ASC');
		$this->c->orderBy('bbb', Dibi::DESC);
		$this->a('SELECT * FROM `datasourcecollection` ORDER BY `aaa` ASC, `bbb` DESC');
		$this->c->orderBy('bbb');
		$this->markTestSkipped('datasource nepodporuje sort vicekrat podle stejneho klice');
		$this->a('SELECT * FROM `datasourcecollection` ORDER BY `aaa` ASC, `bbb` DESC, `bbb` ASC');
	}

	public function testSqlSub()
	{
		$this->c->orderBy('aaa');

		$c = $this->c->toCollection()->orderBy('bbb', Dibi::DESC);

		$this->a('SELECT * FROM `datasourcecollection` ORDER BY `bbb` DESC, `aaa` ASC', $c);

		$c->orderBy('xxx');

		$this->a('SELECT * FROM `datasourcecollection` ORDER BY `bbb` DESC, `xxx` ASC, `aaa` ASC', $c);

		$c->orderBy(array());

		$this->a('SELECT * FROM `datasourcecollection` ORDER BY `aaa` ASC');
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
