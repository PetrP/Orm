<?php

use Orm\RelationshipMetaDataOneToOne;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToOne::__construct
 * @covers Orm\RelationshipMetaDataToOne::__construct
 * @covers Orm\RelationshipMetaData::__construct
 */
class RelationshipMetaDataOneToOne_construct_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataOneToOne('Entity', 'parentParam', 'repo', 'param');
		$this->assertInstanceOf('Orm\RelationshipMetaDataToOne', $l);
		$this->assertInstanceOf('Orm\RelationshipMetaData', $l);
	}

	public function testNoRepo()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'Entity::$foo {1:1} You must specify foreign repository {1:1 repositoryName}');
		new RelationshipMetaDataOneToOne('Entity', 'foo', '', 'param');
	}

	public function testDefaultParam()
	{
		$rl = new RelationshipMetaDataOneToOne('Entity', 'foo', 'repo', '');
		$this->assertSame('', $rl->getChildParam());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToOne', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
