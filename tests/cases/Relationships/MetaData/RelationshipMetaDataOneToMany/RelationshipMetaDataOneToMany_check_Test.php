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

	public function testCheckParam()
	{
		$rl = new RelationshipMetaDataOneToMany('Entity', 'foo', 'tests', 'xxxx', 'Orm\OneToMany');
		$this->setExpectedException('Orm\RelationshipLoaderException', 'Entity::$foo {1:m} na druhe strane asociace tests::$xxxx neni asociace ktera by ukazovala zpet');
		$rl->check(new RepositoryContainer);
	}

	public function testCheckParamEmpty()
	{
		$rl = new RelationshipMetaDataOneToMany('Entity', 'foo', 'tests', '', 'Orm\OneToMany');
		$this->setExpectedException('Orm\RelationshipLoaderException', 'Entity::$foo {1:m} na druhe strane asociace tests::$entity neni asociace ktera by ukazovala zpet');
		$rl->check(new RepositoryContainer);
	}

	public function testOk()
	{
		$rl = new RelationshipMetaDataOneToMany('OneToManyX_Entity', 'many', 'OneToMany_Repository', 'param', 'Orm\OneToMany');
		$rl->check(new RepositoryContainer);
		$this->assertTrue(true);
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
