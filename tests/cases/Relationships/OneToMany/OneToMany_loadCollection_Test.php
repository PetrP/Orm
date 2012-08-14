<?php

use Orm\RelationshipMetaDataOneToMany;

/**
 * @covers Orm\OneToMany::loadCollection
 */
class OneToMany_loadCollection_Test extends OneToMany_Test
{

	public function testFindByRepo()
	{
		$o2m = new OneToMany_OneToMany($this->e, new RelationshipMetaDataOneToMany(get_class($this->e), 'id', 'OneToMany_2', 'param'));
		$o2m->_getCollection();
		$this->assertSame(1, $this->e->model->OneToMany_2->count);
		$this->assertSame(0, $this->e->model->OneToMany_2->mapper->count);
	}

	public function testFindByMapper()
	{
		$o2m = new OneToMany_OneToMany($this->e, new RelationshipMetaDataOneToMany(get_class($this->e), 'id', 'OneToMany_3', 'param'));
		$o2m->_getCollection();
		$this->assertSame(1, $this->e->model->OneToMany_3->mapper->count);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'loadCollection');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
