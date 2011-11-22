<?php

use Orm\RelationshipMetaDataManyToOne;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToOne::__construct
 * @covers Orm\RelationshipMetaDataToOne::__construct
 * @covers Orm\RelationshipMetaData::__construct
 */
class RelationshipMetaDataManyToOne_construct_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataManyToOne('Entity', 'parentParam', 'repo', 'param');
		$this->assertInstanceOf('Orm\RelationshipMetaDataToOne', $l);
		$this->assertInstanceOf('Orm\RelationshipMetaData', $l);
	}

	public function testNoRepo()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'Entity::$foo {m:1} You must specify foreign repository {m:1 repositoryName}');
		new RelationshipMetaDataManyToOne('Entity', 'foo', '', 'param');
	}

	public function testDefaultParam()
	{
		$rl = new RelationshipMetaDataManyToOne('Entity', 'foo', 'repo', '');
		$this->assertSame('', $rl->getChildParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToOne', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
