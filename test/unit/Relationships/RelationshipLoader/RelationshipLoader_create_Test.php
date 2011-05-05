<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers RelationshipLoader::create
 */
class RelationshipLoader_create_Test extends OneToMany_Test
{
	private $l;
	protected function setUp()
	{
		parent::setUp();
		$this->l = new RelationshipLoader(MetaData::OneToMany, 'OneToMany', $this->r->getRepositoryName(), 'param', get_class($this->e), 'many');
	}

	public function testInjection()
	{
		$this->assertInstanceOf('IEntityInjectionLoader', $this->l);
	}

	public function testReturn()
	{
		$o2m = $this->l->create('OneToMany', $this->e, array(10,11,12,13));
		$this->assertInstanceOf('OneToMany', $o2m);
	}

	public function testMultipleCreate()
	{
		$o2m1 = $this->l->create('OneToMany', $this->e, array(10,11,12,13));
		$o2m2 = $this->l->create('OneToMany', $this->e, array(10,11,12,13));
		$this->assertNotSame($o2m1, $o2m2);
	}

	public function testValue()
	{
		$this->o2m = $this->l->create('OneToMany', $this->e, array(10,11,12,13));
		$this->t(10,11,12,13);
	}

	public function testBadClass()
	{
		$this->setExpectedException('InvalidStateException');
		$this->l->create('ManyToMany', $this->e, array(10,11,12,13));
	}

}
