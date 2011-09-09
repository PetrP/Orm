<?php

use Orm\RelationshipLoader;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipLoader::getParam
 */
class RelationshipLoader_getParam_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipLoader(MetaData::OneToMany, 'Orm\OneToMany', 'repo', 'param', 'Entity', 'parentParam');
		$this->assertSame('param', $l->getParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipLoader', 'getParam');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
