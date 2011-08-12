<?php

use Orm\DataSourceCollection;

/**
 * @covers Orm\DataSourceCollection::findBy
 * @covers Orm\FindByHelper::dibiProcess
 */
class DataSourceCollection_findBy_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$c = $this->c->findBy(array('x' => 'y'));
		$this->assertInstanceOf('Orm\DataSourceCollection', $c);
		$this->assertNotSame($this->c, $c);
		$this->assertAttributeSame(array(), 'findBy', $this->c);
		$this->assertAttributeSame(array(array('x' => 'y')), 'findBy', $c);
	}

	public function testBase()
	{
		$c = $this->c->findBy(array('x' => 'y'));
		$this->a("SELECT * FROM `datasourcecollection` WHERE (`x` = 'y')", $c);
	}

	public function testEntity()
	{
		$c = $this->c->findBy(array('x' => $this->model->tests->getById(2)));
		$this->a("SELECT * FROM `datasourcecollection` WHERE (`x` = '2')", $c);
	}

	public function testDate()
	{
		$c = $this->c->findBy(array('x' => new DateTime('2011-11-11')));
		$this->a("SELECT * FROM `datasourcecollection` WHERE (`x` = '2011-11-11 00:00:00')", $c);
	}

	public function testNull()
	{
		$c = $this->c->findBy(array('x' => NULL));
		$this->a("SELECT * FROM `datasourcecollection` WHERE (`x` IS NULL)", $c);
	}

	public function testAnd()
	{
		$c = $this->c->findBy(array('x' => 1, 'y' => 2));
		$this->a("SELECT * FROM `datasourcecollection` WHERE (`x` = '1') AND (`y` = '2')", $c);
	}

	public function testArray()
	{
		$c = $this->c->findBy(array('x' => array('a', 'b')));
		$this->a("SELECT * FROM `datasourcecollection` WHERE (`x` IN ('a', 'b'))", $c);
	}

	public function testArrayEmpty()
	{
		$c = $this->c->findBy(array('x' => array()));
		$this->a("SELECT * FROM `datasourcecollection` WHERE (`x` IN (NULL))", $c);
	}

	public function testCollection()
	{
		$c = $this->c->findBy(array('x' => $this->model->tests->mapper->findById(array(1,2))));
		$this->a("SELECT * FROM `datasourcecollection` WHERE (`x` IN (1, 2))", $c);
	}

	public function testArrayEntity()
	{
		$c = $this->c->findBy(array('x' => array($this->model->tests->getById(2), $this->model->tests->getById(1))));
		$this->a("SELECT * FROM `datasourcecollection` WHERE (`x` IN (2, 1))", $c);
	}

}
