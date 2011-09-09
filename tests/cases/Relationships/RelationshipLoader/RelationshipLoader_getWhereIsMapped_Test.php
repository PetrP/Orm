<?php

use Orm\RelationshipLoader;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipLoader::getWhereIsMapped
 */
class RelationshipLoader_getWhereIsMapped_Test extends TestCase
{

	public function test1()
	{
		$l = new RelationshipLoader(MetaData::OneToMany, 'Orm\OneToMany', 'repo', 'param', 'Entity', 'parentParam');
		$this->assertSame(RelationshipLoader::MAPPED_THERE, $l->getWhereIsMapped());
	}

	public function test2()
	{
		$l = new RelationshipLoader(MetaData::ManyToMany, 'Orm\ManyToMany', 'repo', 'param', 'Entity', 'parentParam');
		$this->assertSame(RelationshipLoader::MAPPED_THERE, $l->getWhereIsMapped());
	}

	public function test3()
	{
		$l = new RelationshipLoader(MetaData::ManyToMany, 'Orm\ManyToMany', 'repo', 'param', 'Entity', 'parentParam', RelationshipLoader::MAPPED_HERE);
		$this->assertSame(RelationshipLoader::MAPPED_HERE, $l->getWhereIsMapped());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipLoader', 'getWhereIsMapped');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
