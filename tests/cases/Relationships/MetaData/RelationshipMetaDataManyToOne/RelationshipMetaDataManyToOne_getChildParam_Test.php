<?php

use Orm\RelationshipMetaDataManyToOne;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToOne::getChildParam
 */
class RelationshipMetaDataManyToOne_getChildParam_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataManyToOne('Entity', 'parentParam', 'repo', 'param');
		$this->assertSame('param', $l->getChildParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToOne', 'getChildParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
