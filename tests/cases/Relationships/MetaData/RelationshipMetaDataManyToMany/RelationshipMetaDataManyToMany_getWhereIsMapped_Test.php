<?php

use Orm\RelationshipMetaDataManyToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToMany::getWhereIsMapped
 */
class RelationshipMetaDataManyToMany_getWhereIsMapped_Test extends TestCase
{

	public function test2()
	{
		$l = new RelationshipMetaDataManyToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\ManyToMany');
		$this->assertSame(RelationshipMetaDataManyToMany::MAPPED_THERE, $l->getWhereIsMapped());
	}

	public function test3()
	{
		$l = new RelationshipMetaDataManyToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\ManyToMany', RelationshipMetaDataManyToMany::MAPPED_HERE);
		$this->assertSame(RelationshipMetaDataManyToMany::MAPPED_HERE, $l->getWhereIsMapped());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToMany', 'getWhereIsMapped');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
