<?php

use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\ManyToMany::createEntity
 * @covers Orm\BaseToMany::createEntity
 */
class ManyToMany_createEntity_NotInvasive_Test extends ManyToMany_Test
{
	private function tt($enter)
	{
		return $this->m2m->__createEntity($enter, false);
	}

	public function testEntity()
	{
		$e = new OneToMany_Entity;
		$this->assertSame($e, $this->tt($e));
		$this->assertSame(NULL, $e->getRepository(false));
	}

	public function testBad()
	{
		$this->assertSame(NULL, $this->tt(new TestEntity));
	}

	public function testId()
	{
		$e = $this->r->getById(11);
		$this->assertSame($e, $this->tt(11));
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testIdNotFound()
	{
		$this->assertSame(NULL, $this->tt(333));
	}

	public function testArray()
	{
		$e = $this->tt(array('string' => 'xyz'));
		$this->assertSame(NULL, $e);
	}

	public function testArrayWithId()
	{
		$e = $this->r->getById(11);
		$ee = $this->tt(array('id' => 11, 'string' => 'xyz'));
		$this->assertSame($e, $ee);
		$this->assertSame('', $e->string);
		$this->assertSame($this->r, $e->getRepository());
	}

	public function testArrayWithIdNotFound()
	{
		$e = $this->tt(array('id' => 333, 'string' => 'xyz'));
		$this->assertSame(NULL, $e);
	}

	public function testNotAttached()
	{
		$this->m2m = new ManyToMany_ManyToMany(new ManyToMany_Entity, new RelationshipMetaDataManyToMany('ManyToMany_Entity', 'param', 'OneToMany_', 'param', NULL, true), array(10,11,12,13));
		$e = $this->tt(11);
		$this->assertSame(NULL, $e);
	}

	public function testWipeGet()
	{
		$c = $this->m2m->_getCollection();
		$this->assertAttributeSame($c, 'get', $this->m2m);
		$this->tt(11);
		$this->assertAttributeSame($c, 'get', $this->m2m);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'createEntity');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
