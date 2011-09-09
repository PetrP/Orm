<?php

use Orm\RelationshipLoader;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipLoader::getRepository
 */
class RelationshipLoader_getRepository_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipLoader(MetaData::OneToMany, 'Orm\OneToMany', 'repo', 'param', 'Entity', 'parentParam');
		$this->assertSame('repo', $l->getRepository());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipLoader', 'getRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
