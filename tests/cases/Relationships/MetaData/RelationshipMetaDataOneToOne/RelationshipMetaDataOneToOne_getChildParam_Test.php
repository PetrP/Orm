<?php

use Orm\RelationshipMetaDataOneToOne;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToOne::getChildParam
 */
class RelationshipMetaDataOneToOne_getChildParam_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataOneToOne('Entity', 'parentParam', 'repo', 'param');
		$this->assertSame('param', $l->getChildParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToOne', 'getChildParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
