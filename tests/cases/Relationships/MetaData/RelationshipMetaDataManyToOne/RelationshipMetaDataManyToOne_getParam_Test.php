<?php

use Orm\RelationshipMetaDataManyToOne;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToOne::getParam
 */
class RelationshipMetaDataManyToOne_getParam_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataManyToOne('Entity', 'parentParam', 'repo', 'param');
		$this->assertSame('param', $l->getParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToOne', 'getParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
