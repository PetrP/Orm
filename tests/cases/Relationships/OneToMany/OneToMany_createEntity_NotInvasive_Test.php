<?php

use Orm\RelationshipMetaDataOneToMany;

/**
 * @covers Orm\OneToMany::createEntity
 * @covers Orm\BaseToMany::createEntity
 */
class OneToMany_createEntity_NotInvasive_Test extends OneToMany_Test
{

	private function tt($enter)
	{
		return $this->o2m->__createEntity($enter, false);
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
		$this->o2m = new OneToMany_OneToMany(new TestEntity, new RelationshipMetaDataOneToMany('TestEntity', 'id', 'OneToMany_', 'param'));
		$e = $this->tt(11);
		$this->assertSame(NULL, $e);
	}

	public function testWipeGet()
	{
		$c = $this->o2m->_getCollection();
		$this->assertAttributeSame($c, 'get', $this->o2m);
		$this->tt(11);
		$this->assertAttributeSame($c, 'get', $this->o2m);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'createEntity');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
