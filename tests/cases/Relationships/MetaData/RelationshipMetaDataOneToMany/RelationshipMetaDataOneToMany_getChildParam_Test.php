<?php

use Orm\RelationshipMetaDataOneToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToMany::getChildParam
 */
class RelationshipMetaDataOneToMany_getChildParam_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataOneToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\OneToMany');
		$this->assertSame('param', $l->getChildParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToMany', 'getChildParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
