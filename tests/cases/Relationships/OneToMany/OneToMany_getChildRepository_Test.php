<?php

use Orm\OneToMany;
use Orm\RelationshipMetaDataOneToMany;

/**
 * @covers Orm\OneToMany::getChildRepository
 */
class OneToMany_getChildRepository_Test extends OneToMany_Test
{

	public function testPersist_RepoName()
	{
		$this->o2m = new MockOneToMany($this->e, new RelationshipMetaDataOneToMany(get_class($this->e), 'id', get_class($this->r), 'param'));
		$this->assertSame($this->r, $this->o2m->getCR());
		$this->assertSame($this->r, $this->o2m->getCR(true));
		$this->assertSame($this->r, $this->o2m->getCR(false));
	}

	public function testNotPersist_RepoName1()
	{
		$this->o2m = new MockOneToMany(new TestEntity, new RelationshipMetaDataOneToMany('TestEntity', 'id', get_class($this->r), 'param'));
		$this->setExpectedException('Orm\EntityNotAttachedException', 'TestEntity is not attached to repository.');
		$this->o2m->getCR();
	}

	public function testNotPersist_RepoName2()
	{
		$this->o2m = new MockOneToMany(new TestEntity, new RelationshipMetaDataOneToMany('TestEntity', 'id', get_class($this->r), 'param'));
		$this->setExpectedException('Orm\EntityNotAttachedException', 'TestEntity is not attached to repository.');
		$this->o2m->getCR(true);
	}

	public function testNotPersist_RepoName3()
	{
		$this->o2m = new MockOneToMany(new TestEntity, new RelationshipMetaDataOneToMany('TestEntity', 'id', get_class($this->r), 'param'));
		$this->assertSame(NULL, $this->o2m->getCR(false));
	}

	public function testNotPersist_RepoName_null()
	{
		$this->o2m = new MockOneToMany(new TestEntity, new RelationshipMetaDataOneToMany('TestEntity', 'id', get_class($this->r), 'param'));
		$this->assertSame(NULL, $this->o2m->getCR(NULL));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseToMany', 'getChildRepository');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

class MockOneToMany extends OneToMany
{
	public function getCR($need = true)
	{
		if (func_num_args())
		{
			return $this->getChildRepository($need);
		}
		return $this->getChildRepository();
	}
}
