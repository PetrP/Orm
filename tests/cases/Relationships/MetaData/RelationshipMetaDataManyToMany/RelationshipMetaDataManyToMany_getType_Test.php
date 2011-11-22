<?php

use Orm\RelationshipMetaDataManyToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToMany::getType
 */
class RelationshipMetaDataManyToMany_getType_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataManyToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\ManyToMany');
		$this->assertSame(MetaData::ManyToMany, $l->getType());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToMany', 'getType');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
