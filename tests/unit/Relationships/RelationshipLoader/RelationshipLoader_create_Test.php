<?php

use Orm\RelationshipLoader;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipLoader::create
 */
class RelationshipLoader_create_Test extends OneToMany_Test
{
	private $l;
	protected function setUp()
	{
		parent::setUp();
		$this->l = new RelationshipLoader(MetaData::OneToMany, 'Orm\OneToMany', get_class($this->r), 'param', get_class($this->e), 'many');
	}

	public function testInjection()
	{
		$this->assertInstanceOf('Orm\IEntityInjectionLoader', $this->l);
	}

	public function testReturn()
	{
		$o2m = $this->l->create('Orm\OneToMany', $this->e, array(10,11,12,13));
		$this->assertInstanceOf('Orm\OneToMany', $o2m);
	}

	public function testMultipleCreate()
	{
		$o2m1 = $this->l->create('Orm\OneToMany', $this->e, array(10,11,12,13));
		$o2m2 = $this->l->create('Orm\OneToMany', $this->e, array(10,11,12,13));
		$this->assertNotSame($o2m1, $o2m2);
	}

	public function testValue()
	{
		$this->l = new RelationshipLoader(MetaData::OneToMany, 'OneToMany_OneToMany', get_class($this->r), 'param', get_class($this->e), 'many');
		$this->o2m = $this->l->create('OneToMany_OneToMany', $this->e, array(10,11,12,13));
		$this->t(10,11,12,13);
	}

	public function testBadClass()
	{
		$this->setExpectedException('Nette\InvalidStateException');
		$this->l->create('Orm\ManyToMany', $this->e, array(10,11,12,13));
	}

}
