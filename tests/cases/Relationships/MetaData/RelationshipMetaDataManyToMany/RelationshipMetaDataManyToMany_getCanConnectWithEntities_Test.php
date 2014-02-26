<?php

use Orm\MetaData;
use Orm\RepositoryContainer;
use Orm\RelationshipMetaDataManyToMany;


/**
 * @covers Orm\RelationshipMetaDataManyToMany::getCanConnectWithEntities
 */
class RelationshipMetaDataManyToMany_getCanConnectWithEntities_Test extends TestCase
{

	public function test()
	{
		$r = new RepositoryContainer;
		$meta = MetaData::getEntityRules('Association_Entity');
		$meta = $meta['manyToMany1']['relationshipParam'];
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToMany', $meta);
		$this->assertSame(array(
			'association_entity' => 'Association_Entity',
		), $meta->getCanConnectWithEntities($r));
	}

	public function test2()
	{
		$r = new RepositoryContainer;
		$meta = new RelationshipMetaDataManyToMany('Association_Entity', 'manyToMany1', 'Association_Repository', 'manyToMany2', NULL, true);
		$this->assertSame(array(
			'association_entity' => 'Association_Entity',
		), $meta->getCanConnectWithEntities($r));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToMany', 'getCanConnectWithEntities');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
