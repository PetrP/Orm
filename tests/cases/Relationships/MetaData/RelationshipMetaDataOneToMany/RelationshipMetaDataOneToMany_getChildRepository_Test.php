<?php

use Orm\RelationshipMetaDataOneToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataOneToMany::getChildRepository
 */
class RelationshipMetaDataOneToMany_getChildRepository_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataOneToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\OneToMany');
		$this->assertSame('repo', $l->getChildRepository());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToMany', 'getChildRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
