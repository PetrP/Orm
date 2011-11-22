<?php

use Orm\RelationshipMetaDataManyToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToMany::getChildParam
 */
class RelationshipMetaDataManyToMany_getChildParam_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataManyToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\ManyToMany');
		$this->assertSame('param', $l->getChildParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToMany', 'getChildParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
