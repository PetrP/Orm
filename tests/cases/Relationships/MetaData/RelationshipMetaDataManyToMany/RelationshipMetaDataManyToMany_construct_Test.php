<?php

use Orm\RelationshipMetaDataManyToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToMany::__construct
 * @covers Orm\RelationshipMetaDataToMany::__construct
 * @covers Orm\RelationshipMetaDataToMany::setRelationshipClass
 * @covers Orm\RelationshipMetaData::__construct
 */
class RelationshipMetaDataManyToMany_construct_Test extends TestCase
{
	private function t($param)
	{
	}

	public function test()
	{
		$rl = new RelationshipMetaDataManyToMany('Entity', 'foo', 'repo', 'foo', 'Orm\ManyToMany');
		$this->assertInstanceOf('Orm\RelationshipMetaDataToMany', $rl);
		$this->assertInstanceOf('Orm\RelationshipMetaData', $rl);
		$this->assertInstanceOf('Orm\IEntityInjectionLoader', $rl);
	}

	public function testNoRepo()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'Entity::$foo {m:m} You must specify foreign repository {m:m repositoryName param}');
		new RelationshipMetaDataManyToMany('Entity', 'foo', '', 'param', 'Orm\ManyToMany');
	}

	public function testMappedHere()
	{
		$rl = new RelationshipMetaDataManyToMany('Entity', 'foo', 'repo', 'foo', 'Orm\ManyToMany', RelationshipMetaDataManyToMany::MAPPED_HERE);
		$this->assertSame(RelationshipMetaDataManyToMany::MAPPED_HERE, $rl->getWhereIsMapped());
	}

	public function testMappedThere()
	{
		$rl = new RelationshipMetaDataManyToMany('Entity', 'foo', 'repo', 'foo', 'Orm\ManyToMany', RelationshipMetaDataManyToMany::MAPPED_THERE);
		$this->assertSame(RelationshipMetaDataManyToMany::MAPPED_THERE, $rl->getWhereIsMapped());
	}

	public function testMappedBoth()
	{
		$rl = new RelationshipMetaDataManyToMany('Entity', 'foo', 'repo', 'foo', 'Orm\ManyToMany', RelationshipMetaDataManyToMany::MAPPED_BOTH);
		// both se neda nastavit, musi se netekovat v check
		$this->assertSame(RelationshipMetaDataManyToMany::MAPPED_HERE, $rl->getWhereIsMapped());
	}

	public function testDefaultClass()
	{
		$rl = new RelationshipMetaDataManyToMany('Entity', 'foo', 'repo', '');
		$this->assertAttributeSame('Orm\ManyToMany', 'relationshipClass', $rl);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToMany', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
