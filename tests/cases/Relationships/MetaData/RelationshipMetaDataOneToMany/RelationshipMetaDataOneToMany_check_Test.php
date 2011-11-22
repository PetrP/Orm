<?php

use Orm\RelationshipMetaDataOneToMany;
use Orm\MetaData;
use Orm\RepositoryContainer;

/**
 * @covers Orm\RelationshipMetaDataOneToMany::check
 * @covers Orm\RelationshipMetaData::check
 * @covers Orm\RelationshipMetaData::checkIntegrity
 */
class RelationshipMetaDataOneToMany_check_Test extends TestCase
{

	public function testCheckRepo()
	{
		$rl = new RelationshipMetaDataOneToMany('Entity', 'foo', 'repo', '', 'Orm\OneToMany');
		$this->setExpectedException('Orm\RelationshipLoaderException', 'repo isn\'t repository in Entity::$foo');
		$rl->check(new RepositoryContainer);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToMany', 'check');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
