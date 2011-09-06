<?php

use Orm\RelationshipLoader;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipLoader::__construct
 */
class RelationshipLoader_construct_Test extends TestCase
{
	private function t($param)
	{
	}

	public function testNoRepo()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'Entity::$foo {1:m} You must specify foreign repository {1:m repositoryName param}');
		new RelationshipLoader(MetaData::OneToMany, 'Orm\OneToMany', '', 'param', 'Entity', 'foo');
	}

	public function testOneToManyDefaultParam()
	{
		$rl = new RelationshipLoader(MetaData::OneToMany, 'Orm\OneToMany', 'repo', '', 'Entity', 'foo');
		$this->assertAttributeSame('entity', 'param', $rl);
	}

	public function testOldToMany()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'Entity::$foo {1:m} You can\'t specify foreign repository for Orm\OldOneToMany');
		new RelationshipLoader(MetaData::OneToMany, 'RelationshipLoader_construct_OldOneToMany', 'repo', '', 'Entity', 'foo');
	}

	public function testMappedHere()
	{
		$rl = new RelationshipLoader(MetaData::ManyToMany, 'Orm\ManyToMany', 'repo', 'foo', 'Entity', 'foo', RelationshipLoader::MAPPED_HERE);
		$this->assertAttributeSame(RelationshipLoader::MAPPED_HERE, 'mapped', $rl);
	}

	public function testMappedThere()
	{
		$rl = new RelationshipLoader(MetaData::ManyToMany, 'Orm\ManyToMany', 'repo', 'foo', 'Entity', 'foo', RelationshipLoader::MAPPED_THERE);
		$this->assertAttributeSame(RelationshipLoader::MAPPED_THERE, 'mapped', $rl);
	}

	public function testMappedBoth()
	{
		$rl = new RelationshipLoader(MetaData::ManyToMany, 'Orm\ManyToMany', 'repo', 'foo', 'Entity', 'foo', RelationshipLoader::MAPPED_BOTH);
		// both se neda nastavit, musi se netekovat v check
		$this->assertAttributeSame(RelationshipLoader::MAPPED_HERE, 'mapped', $rl);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipLoader', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
