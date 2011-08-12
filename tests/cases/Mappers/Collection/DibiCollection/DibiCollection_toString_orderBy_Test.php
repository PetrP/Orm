<?php

/**
 * @covers Orm\DibiCollection::process
 * @covers Orm\DibiCollection::__toString
 * @covers Orm\DibiCollection::connectionTranslate
 */
class DibiCollection_toString_orderBy_Test extends DibiCollection_Base_Test
{

	public function testSql()
	{
		$this->c->orderBy('aaa');
		$this->a('SELECT `e`.* FROM `dibicollection` as e ORDER BY `e`.`aaa` ASC');
		$this->c->orderBy('bbb', Dibi::DESC);
		$this->a('SELECT `e`.* FROM `dibicollection` as e ORDER BY `e`.`aaa` ASC, `e`.`bbb` DESC');
		$this->c->orderBy('bbb');
		$this->a('SELECT `e`.* FROM `dibicollection` as e ORDER BY `e`.`aaa` ASC, `e`.`bbb` DESC, `e`.`bbb` ASC');
		$this->c->orderBy(array());
		$this->a('SELECT `e`.* FROM `dibicollection` as e');
	}

	public function testSqlSub()
	{
		$this->c->orderBy('aaa');

		$c = $this->c->toCollection()->orderBy('bbb', Dibi::DESC);

		$this->a('SELECT `e`.* FROM `dibicollection` as e ORDER BY `e`.`bbb` DESC, `e`.`aaa` ASC', $c);

		$c->orderBy('xxx');

		$this->a('SELECT `e`.* FROM `dibicollection` as e ORDER BY `e`.`bbb` DESC, `e`.`xxx` ASC, `e`.`aaa` ASC', $c);

		$c->orderBy(array());

		$this->a('SELECT `e`.* FROM `dibicollection` as e ORDER BY `e`.`aaa` ASC');
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
