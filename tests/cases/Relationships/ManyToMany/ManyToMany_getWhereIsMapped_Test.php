<?php

use Orm\ManyToMany;
use Orm\RelationshipMetaDataToMany;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\ManyToMany::getWhereIsMapped
 */
class ManyToMany_getWhereIsMapped_Test extends ManyToMany_Test
{

	public function testHere()
	{
		$this->m2m = new ManyToMany($this->e, new RelationshipMetaDataManyToMany(get_class($this->e), 'param', get_class($this->r), 'param', NULL, RelationshipMetaDataToMany::MAPPED_HERE));
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_HERE, $this->m2m->getWhereIsMapped());
	}

	public function testThere()
	{
		$this->m2m = new ManyToMany($this->e, new MockRelationshipMetaDataManyToManyBoth(get_class($this->e), 'param', get_class($this->r), 'param'));
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_BOTH, $this->m2m->getWhereIsMapped());
	}

	public function testBoth()
	{
		$this->m2m = new ManyToMany($this->e, new RelationshipMetaDataManyToMany(get_class($this->e), 'param', get_class($this->r), 'param', NULL, RelationshipMetaDataToMany::MAPPED_THERE));
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_THERE, $this->m2m->getWhereIsMapped());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'getWhereIsMapped');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
