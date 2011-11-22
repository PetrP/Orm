<?php

use Orm\RelationshipMetaDataOneToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToMany::getParam
 */
class RelationshipMetaDataOneToMany_getParam_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataOneToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\OneToMany');
		$this->assertSame('param', $l->getParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToMany', 'getParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
