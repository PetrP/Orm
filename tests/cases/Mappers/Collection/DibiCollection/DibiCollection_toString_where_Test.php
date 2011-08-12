<?php

/**
 * @covers Orm\DibiCollection::__toString
 * @covers Orm\DibiCollection::connectionTranslate
 */
class DibiCollection_toString_where_Test extends DibiCollection_Base_Test
{

	public function testSql()
	{
		$this->c->where('1=1');
		$this->a('SELECT `e`.* FROM `dibicollection` as e WHERE (1=1)');
		$this->c->where('2=2');
		$this->a('SELECT `e`.* FROM `dibicollection` as e WHERE (1=1) AND (2=2)');
		$this->c->where(array('`bb` = `aa`'));
		$this->a('SELECT `e`.* FROM `dibicollection` as e WHERE (1=1) AND (2=2) AND (`bb` = `aa`)');
		$this->c->where('%n = %s', 'foo', 'bar');
		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (1=1) AND (2=2) AND (`bb` = `aa`) AND (`foo` = 'bar')");
	}

	public function testSqlSub()
	{
		$this->c->where('1=1');

		$c = $this->c->toCollection()->where('2=2');

		$this->a('SELECT `e`.* FROM `dibicollection` as e WHERE (1=1) AND (2=2)', $c);

		$c->where('%n = %s', 'foo', 'bar');

		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (1=1) AND (2=2) AND (`foo` = 'bar')", $c);

	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiCollection', '__toString');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
