<?php

use Orm\RelationshipMetaDataOneToMany;

/**
 * @covers Orm\OneToMany::add
 */
class OneToMany_add_Test extends OneToMany_Test
{

	public function testNew()
	{
		$e = new OneToMany_Entity;
		$this->o2m->add($e);
		$this->t(10,11,12,13, $e);
		$this->o2m->remove($e);
		$this->t(10,11,12,13);
		$this->o2m->add($e);
		$this->t(10,11,12,13, $e);
	}

	public function testExist()
	{
		$this->o2m->remove(13);
		$this->o2m->remove(11);
		$this->t(10,12);
		$this->o2m->add(13);
		$this->t(10,12,13);
		$this->o2m->add(11);
		$this->t(10,12,13,11);
	}

	public function testMultipleSame()
	{
		$this->o2m->remove(11);
		$this->t(10,12,13);
		$this->o2m->add(11);
		$this->t(10,12,13,11);
		$this->o2m->add(11);
		$this->t(10,12,13,11);
	}

	public function testBad()
	{
		$this->setExpectedException('Orm\InvalidEntityException', "OneToMany_Repository can't work with entity 'TestEntity'");
		$this->o2m->add(new TestEntity);
	}

	public function testBad2()
	{
		$e = new OneToMany_Entity;
		$e->param = new OneToManyX_Entity;
		$this->setExpectedException('Orm\InvalidEntityException', 'Entity OneToMany_Entity is already asociated with another entity.');
		$this->o2m->add($e);
	}

	public function testChanged()
	{
		$this->assertFalse($this->e->isChanged());
		$this->assertFalse($this->e->isChanged('id'));
		$this->assertFalse($this->e->isChanged('many'));
		$this->assertFalse($this->e->isChanged('string'));
		$this->o2m->add(11);
		$this->assertTrue($this->e->isChanged());
		$this->assertFalse($this->e->isChanged('id'));
		$this->assertTrue($this->e->isChanged('many'));
		$this->assertFalse($this->e->isChanged('string'));
	}

	public function testWipeGet()
	{
		$this->o2m->_getCollection();
		$this->assertAttributeInstanceOf('Orm\IEntityCollection', 'get', $this->o2m);
		$this->o2m->add(11);
		$this->assertAttributeSame(NULL, 'get', $this->o2m);
	}

	public function testInvalidChildParam()
	{
		$this->meta1 = new RelationshipMetaDataOneToMany(get_class($this->e), 'many', 'OneToMany_', 'id');
		$this->o2m = new OneToMany_OneToMany($this->e, $this->meta1);
		$this->setExpectedException('Orm\NotValidException', 'OneToMany_Entity::$id::$many must be instanceof Orm\OneToMany; \'NULL\' given.');
		$this->o2m->add(12);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'add');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
