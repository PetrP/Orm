<?php

use Orm\MetaData;
use Orm\RepositoryContainer;


/**
 * @covers Orm\RelationshipMetaDataOneToOne::getCanConnectWithEntities
 */
class RelationshipMetaDataOneToOne_getCanConnectWithEntities_Test extends TestCase
{

	public function test()
	{
		$r = new RepositoryContainer;
		$meta = MetaData::getEntityRules('Association_Entity');
		$meta = $meta['oneToOne1']['relationshipParam'];
		$this->assertInstanceOf('Orm\RelationshipMetaDataOneToOne', $meta);
		$this->assertSame(array(
			'association_entity' => 'Association_Entity',
		), $meta->getCanConnectWithEntities($r));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToOne', 'getCanConnectWithEntities');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
