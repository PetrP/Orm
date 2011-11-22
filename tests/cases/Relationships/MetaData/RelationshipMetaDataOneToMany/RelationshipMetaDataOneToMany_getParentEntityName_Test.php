<?php

use Orm\RelationshipMetaDataOneToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToMany::getParentEntityName
 */
class RelationshipMetaDataOneToMany_getParentEntityName_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataOneToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\OneToMany');
		$this->assertSame('Entity', $l->getParentEntityName());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToMany', 'getParentEntityName');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
