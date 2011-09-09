<?php

use Orm\RelationshipLoader;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipLoader::getParentParam
 */
class RelationshipLoader_getParentParam_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipLoader(MetaData::OneToMany, 'Orm\OneToMany', 'repo', 'param', 'Entity', 'parentParam');
		$this->assertSame('parentParam', $l->getParentParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipLoader', 'getParentParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
