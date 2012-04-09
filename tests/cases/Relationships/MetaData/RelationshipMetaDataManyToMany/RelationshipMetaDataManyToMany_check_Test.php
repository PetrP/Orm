<?php

use Orm\MetaData;
use Orm\RepositoryContainer;
use Orm\RelationshipLoaderException;
use Orm\RelationshipMetaDataToMany;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\RelationshipMetaDataManyToMany::check
 * @covers Orm\RelationshipMetaData::check
 * @covers Orm\RelationshipMetaData::checkIntegrity
 * @covers Orm\RelationshipMetaDataManyToMany::checkIntegrityCallback
 * @covers Orm\MetaDataProperty::check
 * @covers Orm\RelationshipMetaDataManyToMany::__construct
 * @covers Orm\RelationshipMetaDataManyToMany::setRelationshipClass
 */
class RelationshipMetaDataManyToMany_check_Test extends TestCase
{
	private function t($param)
	{
		MetaData::clean();
		RelationshipMetaDataManyToMany_ManyToMany1_Entity::$param = $param;
		$many = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', new RepositoryContainer);
		if (isset($many['manyX'])) return $many['manyX']['relationshipParam'];
		return $many['many']['relationshipParam'];
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipMetaDataManyToMany_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipMetaDataManyToMany_ManyToMany2_::$unexist neni asociace ktera by ukazovala zpet');
		$this->t('unexist');
	}

	public function testNotRelationship()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipMetaDataManyToMany_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipMetaDataManyToMany_ManyToMany2_Entity::$id neni asociace ktera by ukazovala zpet');
		$this->t('id');
	}

	public function testParamEmpty()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipMetaDataManyToMany_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipMetaDataManyToMany_ManyToMany2_Entity::$manyEmptyParam neni vyplnen param ktery by ukazoval zpet');
		$this->t('manyEmptyParam');
	}

	public function testAnotherParam()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipMetaDataManyToMany_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipMetaDataManyToMany_ManyToMany2_Entity::$manyAnotherParam neukazuje zpet; ukazuje na jiny parametr (manyAnotherParam)');
		$this->t('manyAnotherParam');
	}

	public function testAnotherRepo()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipMetaDataManyToMany_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipMetaDataManyToMany_ManyToMany2_Entity::$manyAnotherRepo neukazuje zpet; ukazuje na jiny repository (RelationshipMetaDataManyToMany_ManyToMany3_)');
		$this->t('manyAnotherRepo');
	}

	public function testOk()
	{
		$loader = $this->t('many');
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToMany', $loader);
	}

	public function testOk2()
	{
		$rl = new RelationshipMetaDataManyToMany('RelationshipMetaDataManyToMany_ManyToMany1_Entity', 'many', 'RelationshipMetaDataManyToMany_ManyToMany2_', 'many', NULL, true);
		$rl->check(new RepositoryContainer);
		$this->assertTrue(true);
		MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', new RepositoryContainer);
	}

	public function testSubClass()
	{
		$rl = new RelationshipMetaDataManyToMany('RelationshipMetaDataManyToMany_ManyToMany1_Entity', 'manyParent', 'RelationshipMetaDataManyToMany_ManyToMany1_Repository', 'manyParent', NULL, true);
		$rl->check(new RepositoryContainer);
		$this->assertTrue(true);
		MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', new RepositoryContainer);
	}

	public function testSubClassAbstract()
	{
		$rl = new RelationshipMetaDataManyToMany('RelationshipMetaDataManyToMany_ManyToMany1Parent_Entity', 'manyParent', 'RelationshipMetaDataManyToMany_ManyToMany1_Repository', 'manyParent', NULL, true);
		$rl->check(new RepositoryContainer);
		$this->assertTrue(true);
		MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1Parent_Entity', new RepositoryContainer);
	}

	public function testSameMapped()
	{
		$many = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', new RepositoryContainer);
		$loader = $many['same1']['relationshipParam'];
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToMany', $loader);
		$this->assertSame('RelationshipMetaDataManyToMany_ManyToMany1_', $loader->repository);
		$this->assertSame('same1', $loader->param);
		$this->assertSame('same1', $loader->parentParam);
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_BOTH, $loader->whereIsMapped);
	}

	public function testSameNotMapped()
	{
		$many = MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany1_Entity', new RepositoryContainer);
		$loader = $many['same2']['relationshipParam'];
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToMany', $loader);
		$this->assertSame('RelationshipMetaDataManyToMany_ManyToMany1_', $loader->repository);
		$this->assertSame('same2', $loader->param);
		$this->assertSame('same2', $loader->parentParam);
		$this->assertSame(RelationshipMetaDataToMany::MAPPED_BOTH, $loader->whereIsMapped);
	}

	public function testClearCache()
	{
		try {
			$this->t('manyAnotherRepo');
			throw new Exception;
		} catch (RelationshipLoaderException $e) {
			$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipMetaDataManyToMany_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipMetaDataManyToMany_ManyToMany2_Entity::$manyAnotherRepo neukazuje zpet; ukazuje na jiny repository (RelationshipMetaDataManyToMany_ManyToMany3_)');
			$this->t('manyAnotherRepo');
		}
	}

	public function testIgnoreCase()
	{
		$ecn = & RelationshipMetaDataManyToMany_ManyToMany2_Repository::$ecn;
		$old = $ecn;
		$ecn = strtoupper($ecn);
		$loader = $this->t('many');
		$this->assertInstanceOf('Orm\RelationshipMetaDataManyToMany', $loader);
		$ecn = $old;
	}

	public function testMultiUse()
	{
		MetaData::clean();
		new RepositoryContainer;
		MetaData::getEntityRules('RelationshipMetaDataManyToMany_ManyToMany4a_Entity');
		$this->assertTrue(true);
	}

	public function testCheckRepo()
	{
		$rl = new RelationshipMetaDataManyToMany('Entity', 'foo', 'repo', '', 'Orm\ManyToMany');
		$this->setExpectedException('Orm\RelationshipLoaderException', 'repo isn\'t repository in Entity::$foo');
		$rl->check(new RepositoryContainer);
	}

	public function testCheckParam()
	{
		$rl = new RelationshipMetaDataManyToMany('Entity', 'foo', 'tests', 'xxxx', 'Orm\ManyToMany');
		$this->setExpectedException('Orm\RelationshipLoaderException', 'Entity::$foo {m:m} na druhe strane asociace tests::$xxxx neni asociace ktera by ukazovala zpet');
		$rl->check(new RepositoryContainer);
	}

	public function testCheckParamEmpty()
	{
		$rl = new RelationshipMetaDataManyToMany('Entity', 'foo', 'tests', '', 'Orm\ManyToMany');
		$rl->check(new RepositoryContainer);
		$this->assertTrue(true);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipMetaDataManyToMany', 'check');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
