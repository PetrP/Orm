<?php

use Orm\RelationshipMetaDataOneToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToMany::create
 */
class RelationshipMetaDataOneToMany_create_Test extends OneToMany_Test
{
	private $l;
	protected function setUp()
	{
		parent::setUp();
		$this->l = new RelationshipMetaDataOneToMany(get_class($this->e), 'many', get_class($this->r), 'param', 'Orm\OneToMany');
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
		$this->l = new RelationshipMetaDataOneToMany(get_class($this->e), 'many', get_class($this->r), 'param', 'OneToMany_OneToMany');
		$this->o2m = $this->l->create('OneToMany_OneToMany', $this->e, array(10,11,12,13));
		$this->t(10,11,12,13);
	}

	public function testBadClass()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException');
		$this->l->create('Orm\ManyToMany', $this->e, array(10,11,12,13));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToMany', 'create');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
