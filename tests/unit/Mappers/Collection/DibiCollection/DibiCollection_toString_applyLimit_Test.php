<?php

/**
 * @covers Orm\DibiCollection::process
 * @covers Orm\DibiCollection::__toString
 * @covers Orm\DibiCollection::connectionTranslate
 */
class DibiCollection_toString_applyLimit_Test extends DibiCollection_Base_Test
{

	public function testSql()
	{
		$this->c->applyLimit(10, 20);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 10 OFFSET 20');
		$this->c->applyLimit(10, NULL);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 10');
		$this->c->applyLimit(NULL, NULL);
		$this->a('SELECT `e`.* FROM `dibicollection` as e');
	}

	public function testSqlSub()
	{
		$this->c->applyLimit(10, NULL);

		$c = $this->c->toCollection()->applyLimit(5);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 5', $c);

		$c = $this->c->toCollection()->applyLimit(4, 2);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 4 OFFSET 2', $c);

		$c = $this->c->toCollection()->applyLimit(NULL, NULL);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 10', $c);

		$c = $this->c->toCollection()->applyLimit(NULL, 4);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 6 OFFSET 4', $c);

		$c = $this->c->toCollection()->applyLimit(NULL, 15);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 0 OFFSET 10', $c);

		$c = $this->c->toCollection()->applyLimit(50);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 10', $c);

		$c = $this->c->toCollection()->applyLimit(50, 15);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 0 OFFSET 10', $c);
	}

	public function testSqlSub2()
	{
		$this->c->applyLimit(10, 20);

		$c = $this->c->toCollection()->applyLimit(4);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 4 OFFSET 20', $c);

		$c = $this->c->toCollection()->applyLimit(5, 2);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 5 OFFSET 22', $c);

		$c = $this->c->toCollection()->applyLimit(NULL, NULL);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 10 OFFSET 20', $c);

		$c = $this->c->toCollection()->applyLimit(NULL, 5);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 5 OFFSET 25', $c);

		$c = $this->c->toCollection()->applyLimit(NULL, 15);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 0 OFFSET 30', $c);

		$c = $this->c->toCollection()->applyLimit(50);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 10 OFFSET 20', $c);

		$c = $this->c->toCollection()->applyLimit(50, 15);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 0 OFFSET 30', $c);
	}

	public function testSqlSub3()
	{
		$this->c->applyLimit(NULL, 20);

		$c = $this->c->toCollection()->applyLimit(5);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 5 OFFSET 20', $c);

		$c = $this->c->toCollection()->applyLimit(5, 2);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 5 OFFSET 22', $c);

		$c = $this->c->toCollection()->applyLimit(NULL, NULL);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 18446744073709551615 OFFSET 20', $c);

		$c = $this->c->toCollection()->applyLimit(NULL, 5);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 18446744073709551615 OFFSET 25', $c);

		$c = $this->c->toCollection()->applyLimit(50);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 50 OFFSET 20', $c);

		$c = $this->c->toCollection()->applyLimit(50, 15);
		$this->a('SELECT `e`.* FROM `dibicollection` as e LIMIT 50 OFFSET 35', $c);
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
