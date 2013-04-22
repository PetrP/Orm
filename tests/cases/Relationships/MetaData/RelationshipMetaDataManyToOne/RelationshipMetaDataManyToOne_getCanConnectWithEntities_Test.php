<?php

use Orm\MetaData;
use Orm\RepositoryContainer;


/**
 * @covers Orm\RelationshipMetaDataManyToOne::getCanConnectWithEntities
 */
class RelationshipMetaDataManyToOne_getCanConnectWithEntities_Test extends TestCase
{

	public function test()
	{
		$r = new RepositoryContainer;
		$meta = MetaData::getEntityRules('OneToMany_Entity');
		$meta = $meta['param']['relationshipParam'];
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToOne', $meta);
		$this->assertSame(array(
			'onetomanyx_entity' => 'OneToManyX_Entity',
		), $meta->getCanConnectWithEntities($r));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToOne', 'getCanConnectWithEntities');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
