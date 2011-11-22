<?php

use Orm\RelationshipMetaDataManyToMany;
use Orm\MetaData;

/**
 * @covers Orm\RelationshipMetaDataManyToMany::getChildRepository
 */
class RelationshipMetaDataManyToMany_getChildRepository_Test extends TestCase
{

	public function test()
	{
		$l = new RelationshipMetaDataManyToMany('Entity', 'parentParam', 'repo', 'param', 'Orm\ManyToMany');
		$this->assertSame('repo', $l->getChildRepository());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToMany', 'getChildRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
