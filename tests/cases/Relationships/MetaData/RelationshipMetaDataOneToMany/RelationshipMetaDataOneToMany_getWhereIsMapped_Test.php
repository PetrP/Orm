<?php

use Orm\RelationshipMetaDataOneToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToMany::getWhereIsMapped
 */
class RelationshipMetaDataOneToMany_getWhereIsMapped_Test extends TestCase
{

	public function test1()
	{
		$l = new RelationshipMetaDataOneToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\OneToMany');
		$this->assertSame(RelationshipMetaDataOneToMany::MAPPED_THERE, $l->getWhereIsMapped());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToMany', 'getWhereIsMapped');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
