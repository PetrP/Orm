<?php

use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\ManyToMany::remove
 */
class ManyToMany_remove_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->m2m->remove(12);
		$this->t(10,11,13);
		$this->m2m->remove(10);
		$this->t(11,13);
	}

	public function test2()
	{
		$this->m2m->remove(11);
		$this->t(10,12,13);
		$this->m2m->add(11);
		$this->t(10,12,13,11);
		$this->m2m->remove(11);
		$this->t(10,12,13);
	}

	public function testNew()
	{
		$e = new OneToMany_Entity;
		$this->m2m->add($e);
		$this->t(10,11,12,13,$e);
		$this->m2m->remove($e);
		$this->t(10,11,12,13);
	}

	public function testMultipleSame()
	{
		$this->m2m->remove(11);
		$this->m2m->remove(11);
		$this->assertTrue(true);
	}

	public function testBad()
	{
		$this->setExpectedException('Orm\InvalidEntityException', "OneToMany_Repository can't work with entity 'TestEntity'");
		$this->m2m->remove(new TestEntity);
	}

	public function testChanged()
	{
		$this->assertFalse($this->e->isChanged());
		$this->assertFalse($this->e->isChanged('id'));
		$this->assertFalse($this->e->isChanged('foo'));
		$this->m2m->remove(11);
		$this->assertTrue($this->e->isChanged());
		$this->assertTrue($this->e->isChanged('id'));
		$this->assertFalse($this->e->isChanged('foo'));
	}

	public function testWipeGet()
	{
		$this->m2m->_getCollection();
		$this->assertAttributeInstanceOf('Orm\IEntityCollection', 'get', $this->m2m);
		$this->m2m->remove(11);
		$this->assertAttributeSame(NULL, 'get', $this->m2m);
	}

	public function testInvalidChildParam()
	{
		$this->meta1 = new RelationshipMetaDataManyToMany(get_class($this->e), 'id', 'OneToMany_', 'id', NULL, true);
		$this->m2m = new ManyToMany_ManyToMany($this->e, $this->meta1, array(10,11,12,13));
		$this->setExpectedException('Orm\NotValidException', 'Param OneToMany_Entity::$id must be instanceof Orm\ManyToMany; \'12\' given.');
		$this->m2m->remove(12);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'remove');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
