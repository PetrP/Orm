<?php

use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\ManyToMany::__construct
 * @covers Orm\BaseToMany::__construct
 */
class ManyToMany_construct_Test extends ManyToMany_Test
{

	public function testWithRepoName()
	{
		$this->m2m = new ManyToMany_ManyToMany($this->e, new RelationshipMetaDataManyToMany(get_class($this->e), 'param', 'OneToMany_', 'param', NULL, true), array(10,11,12,13));
		$this->t(10,11,12,13);
	}

	public function testBadRepo()
	{
		$this->m2m = new ManyToMany_ManyToMany($this->e, new RelationshipMetaDataManyToMany(get_class($this->e), 'param', 'unexists', 'param', NULL, true));
		$this->setExpectedException('Orm\RepositoryNotFoundException', "Repository 'unexists' doesn't exists");
		$this->m2m->_getCollection();
	}

	public function testNoPersistedEntity_repoName()
	{
		$this->m2m = new ManyToMany_ManyToMany(new TestEntity, new RelationshipMetaDataManyToMany(get_class($this->e), 'param', get_class($this->r), 'param', NULL, true));
		$this->assertInstanceOf('Orm\ArrayCollection', $this->m2m->_getCollection());
		$this->t();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
