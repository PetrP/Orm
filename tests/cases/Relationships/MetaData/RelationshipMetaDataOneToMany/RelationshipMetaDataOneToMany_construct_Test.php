<?php

use Orm\RelationshipMetaDataOneToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToMany::__construct
 * @covers Orm\RelationshipMetaDataToMany::__construct
 * @covers Orm\RelationshipMetaDataToMany::setRelationshipClass
 * @covers Orm\RelationshipMetaData::__construct
 */
class RelationshipMetaDataOneToMany_construct_Test extends TestCase
{
	private function t($param)
	{
	}

	public function test()
	{
		$rl = new RelationshipMetaDataOneToMany('Entity', 'foo', 'repo', '', 'Orm\OneToMany');
		$this->assertInstanceOf('Orm\RelationshipMetaDataToMany', $rl);
		$this->assertInstanceOf('Orm\RelationshipMetaData', $rl);
		$this->assertInstanceOf('Orm\IEntityInjectionLoader', $rl);
	}

	public function testNoRepo()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'Entity::$foo {1:m} You must specify foreign repository {1:m repositoryName param}');
		new RelationshipMetaDataOneToMany('Entity', 'foo', '', 'param', 'Orm\OneToMany');
	}

	public function testOneToManyDefaultParam()
	{
		$rl = new RelationshipMetaDataOneToMany('Entity', 'foo', 'repo', '', 'Orm\OneToMany');
		$this->assertSame('entity', $rl->getChildParam());
	}

	public function testDefaultClass()
	{
		$rl = new RelationshipMetaDataOneToMany('Entity', 'foo', 'repo', '');
		$this->assertAttributeSame('Orm\OneToMany', 'relationshipClass', $rl);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToMany', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
