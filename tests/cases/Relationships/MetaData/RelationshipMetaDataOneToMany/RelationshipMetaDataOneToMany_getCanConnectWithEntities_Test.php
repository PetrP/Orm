<?php

use Orm\MetaData;
use Orm\RepositoryContainer;


/**
 * @covers Orm\RelationshipMetaDataOneToMany::getCanConnectWithEntities
 */
class RelationshipMetaDataOneToMany_getCanConnectWithEntities_Test extends TestCase
{

	public function test()
	{
		$r = new RepositoryContainer;
		$meta = MetaData::getEntityRules('OneToManyX_Entity');
		$meta = $meta['many']['relationshipParam'];
		$this->assertInstanceOf('Orm\RelationshipMetaDataOneToMany', $meta);
		$this->assertSame(array(
			'onetomany_entity' => 'OneToMany_Entity',
			'onetomany_entity2' => 'OneToMany_Entity2',
		), $meta->getCanConnectWithEntities($r));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToMany', 'getCanConnectWithEntities');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
