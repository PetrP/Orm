<?php

use Orm\RelationshipMetaDataOneToOne;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToOne::getRepository
 */
class RelationshipMetaDataOneToOne_getRepository_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataOneToOne('Entity', 'parentParam', 'repo', 'param');
		$this->assertSame('repo', $l->getRepository());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToOne', 'getRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
