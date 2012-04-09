<?php

use Orm\RelationshipMetaDataOneToOne;
use Orm\MetaData;
use Orm\RepositoryContainer;

/**
 * @covers Orm\RelationshipMetaDataOneToOne::check
 * @covers Orm\RelationshipMetaData::check
 * @covers Orm\RelationshipMetaData::checkIntegrity
 */
class RelationshipMetaDataOneToOne_check_Test extends TestCase
{

	public function testCheckRepo()
	{
		$rl = new RelationshipMetaDataOneToOne('Entity', 'foo', 'repo', '');
		$this->setExpectedException('Orm\RelationshipLoaderException', 'repo isn\'t repository in Entity::$foo');
		$rl->check(new RepositoryContainer);
	}

	public function testCheckParam()
	{
		$rl = new RelationshipMetaDataOneToOne('Entity', 'foo', 'tests', 'xxxx');
		$this->setExpectedException('Orm\RelationshipLoaderException', 'Entity::$foo {1:1} na druhe strane asociace tests::$xxxx neni asociace ktera by ukazovala zpet');
		$rl->check(new RepositoryContainer);
	}

	public function testCheckParamEmpty()
	{
		$rl = new RelationshipMetaDataOneToOne('Entity', 'foo', 'tests', '');
		$rl->check(new RepositoryContainer);
		$this->assertTrue(true);
	}

	public function testOk()
	{
		$rl = new RelationshipMetaDataOneToOne('RelationshipMetaDataOneToOne_OneToOne_Entity', 'one', 'RelationshipMetaDataOneToOne_OneToOne_Repository', 'one');
		$rl->check(new RepositoryContainer);
		$this->assertTrue(true);
		MetaData::getEntityRules('RelationshipMetaDataOneToOne_OneToOne_Entity', new RepositoryContainer);
	}

	public function testSubClass()
	{
		$rl = new RelationshipMetaDataOneToOne('RelationshipMetaDataOneToOne_OneToOne_Entity', 'oneParent', 'RelationshipMetaDataOneToOne_OneToOne_Repository', 'oneParent');
		$rl->check(new RepositoryContainer);
		$this->assertTrue(true);
		MetaData::getEntityRules('RelationshipMetaDataOneToOne_OneToOne_Entity', new RepositoryContainer);
	}

	public function testSubClassAbstract()
	{
		$rl = new RelationshipMetaDataOneToOne('RelationshipMetaDataOneToOne_OneToOneParent_Entity', 'oneParent', 'RelationshipMetaDataOneToOne_OneToOne_Repository', 'oneParent');
		$rl->check(new RepositoryContainer);
		$this->assertTrue(true);
		MetaData::getEntityRules('RelationshipMetaDataOneToOne_OneToOneParent_Entity', new RepositoryContainer);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataOneToOne', 'check');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
