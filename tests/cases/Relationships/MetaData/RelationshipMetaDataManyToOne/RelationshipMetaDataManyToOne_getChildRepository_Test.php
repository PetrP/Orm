<?php

use Orm\RelationshipMetaDataManyToOne;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToOne::getChildRepository
 */
class RelationshipMetaDataManyToOne_getChildRepository_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataManyToOne('Entity', 'parentParam', 'repo', 'param');
		$this->assertSame('repo', $l->getChildRepository());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToOne', 'getChildRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
