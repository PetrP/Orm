<?php

use Orm\RelationshipMetaDataOneToOne;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToOne::getParentParam
 */
class RelationshipMetaDataOneToOne_getParentParam_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataOneToOne('Entity', 'parentParam', 'repo', 'param');
		$this->assertSame('parentParam', $l->getParentParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToOne', 'getParentParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
