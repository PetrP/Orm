<?php

use Orm\DibiCollection;

/**
 * @covers Orm\DibiCollection::findBy
 * @covers Orm\FindByHelper::dibiProcess
 */
class DibiCollection_findBy_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$c = $this->c->findBy(array('x' => 'y'));
		$this->assertInstanceOf('Orm\DibiCollection', $c);
		$this->assertNotSame($this->c, $c);
		$this->assertAttributeSame(array(), 'findBy', $this->c);
		$this->assertAttributeSame(array(array('x' => 'y')), 'findBy', $c);
	}

	public function testBase()
	{
		$c = $this->c->findBy(array('x' => 'y'));
		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (`e`.`x` = 'y')", $c);
	}

	public function testEntity()
	{
		$c = $this->c->findBy(array('x' => $this->model->tests->getById(2)));
		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (`e`.`x` = '2')", $c);
	}

	public function testDate()
	{
		$c = $this->c->findBy(array('x' => new DateTime('2011-11-11')));
		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (`e`.`x` = '2011-11-11 00:00:00')", $c);
	}

	public function testNull()
	{
		$c = $this->c->findBy(array('x' => NULL));
		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (`e`.`x` IS NULL)", $c);
	}

	public function testAnd()
	{
		$c = $this->c->findBy(array('x' => 1, 'y' => 2));
		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (`e`.`x` = '1') AND (`e`.`y` = '2')", $c);
	}

	public function testArray()
	{
		$c = $this->c->findBy(array('x' => array('a', 'b')));
		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (`e`.`x` IN ('a', 'b'))", $c);
	}

	public function testArrayEmpty()
	{
		$c = $this->c->findBy(array('x' => array()));
		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (`e`.`x` IN (NULL))", $c);
	}

	public function testCollection()
	{
		$c = $this->c->findBy(array('x' => $this->model->tests->mapper->findById(array(1,2))));
		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (`e`.`x` IN (1, 2))", $c);
	}

	public function testArrayEntity()
	{
		$c = $this->c->findBy(array('x' => array($this->model->tests->getById(2), $this->model->tests->getById(1))));
		$this->a("SELECT `e`.* FROM `dibicollection` as e WHERE (`e`.`x` IN (2, 1))", $c);
	}

	/**
	 * @covers Orm\DibiCollection::release
	 * @covers Orm\BaseDibiCollection::release
	 */
	public function testWipe()
	{
		DibiCollection_DibiCollection::setBase($this->c, 'result', array());
		DibiCollection_DibiCollection::set($this->c, 'count', 666);
		$this->assertAttributeSame(array(), 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);

		$c = $this->c->findBy(array('x' => NULL));

		$this->assertAttributeSame(array(), 'result', $this->c);
		$this->assertAttributeSame(666, 'count', $this->c);
		$this->assertAttributeSame(NULL, 'result', $c);
		$this->assertAttributeSame(NULL, 'count', $c);
	}

}
