<?php

use Orm\RelationshipMetaDataManyToOne;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToOne::getType
 */
class RelationshipMetaDataManyToOne_getType_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataManyToOne('Entity', 'parentParam', 'repo', 'param');
		$this->assertSame(MetaData::ManyToOne, $l->getType());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToOne', 'getType');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
