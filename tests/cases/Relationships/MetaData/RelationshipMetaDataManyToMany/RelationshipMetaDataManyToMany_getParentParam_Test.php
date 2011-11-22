<?php

use Orm\RelationshipMetaDataManyToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToMany::getParentParam
 */
class RelationshipMetaDataManyToMany_getParentParam_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataManyToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\ManyToMany');
		$this->assertSame('parentParam', $l->getParentParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToMany', 'getParentParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
