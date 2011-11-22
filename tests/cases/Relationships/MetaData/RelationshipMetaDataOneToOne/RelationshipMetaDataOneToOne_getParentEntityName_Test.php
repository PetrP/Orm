<?php

use Orm\RelationshipMetaDataOneToOne;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToOne::getParentEntityName
 */
class RelationshipMetaDataOneToOne_getParentEntityName_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataOneToOne('Entity', 'parentParam', 'repo', 'param');
		$this->assertSame('Entity', $l->getParentEntityName());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToOne', 'getParentEntityName');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
