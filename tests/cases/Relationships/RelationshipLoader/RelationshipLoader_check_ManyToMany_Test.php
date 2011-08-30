<?php

use Orm\MetaData;
use Orm\RepositoryContainer;
use Orm\RelationshipLoaderException;

/**
 * @covers Orm\RelationshipLoader::check
 * @covers Orm\MetaDataProperty::check
 * @covers Orm\RelationshipLoader::__construct
 */
class RelationshipLoader_check_ManyToMany_Test extends TestCase
{
	private function t($param)
	{
		MetaData::clean();
		RelationshipLoader_ManyToMany1_Entity::$param = $param;
		$many = MetaData::getEntityRules('RelationshipLoader_ManyToMany1_Entity', new RepositoryContainer);
		if (isset($many['manyX'])) return $many['manyX']['relationshipParam'];
		return $many['many']['relationshipParam'];
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_::$unexist neni asociace ktera by ukazovala zpet');
		$this->t('unexist');
	}

	public function testNotRelationship()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$id neni asociace ktera by ukazovala zpet');
		$this->t('id');
	}

	public function testParamEmpty()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyEmptyParam neni vyplnen param ktery by ukazoval zpet');
		$this->t('manyEmptyParam');
	}

	public function testAnotherParam()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherParam neukazuje zpet; ukazuje na jiny parametr (manyAnotherParam)');
		$this->t('manyAnotherParam');
	}

	public function testAnotherRepo()
	{
		$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherRepo neukazuje zpet; ukazuje na jiny repository (RelationshipLoader_ManyToMany3_)');
		$this->t('manyAnotherRepo');
	}

	public function testOk()
	{
		$loader = $this->t('many');
		$this->assertInstanceOf('Orm\RelationshipLoader', $loader);
	}

	public function testSameMapped()
	{
		$many = MetaData::getEntityRules('RelationshipLoader_ManyToMany1_Entity', new RepositoryContainer);
		$loader = $many['same1']['relationshipParam'];
		$this->assertInstanceOf('Orm\RelationshipLoader', $loader);
		$this->assertAttributeSame('RelationshipLoader_ManyToMany1_', 'repository', $loader);
		$this->assertAttributeSame('same1', 'param', $loader);
		$this->assertAttributeSame('same1', 'parentParam', $loader);
		$this->assertAttributeSame(true, 'mappedByThis', $loader);
	}

	public function testSameNotMapped()
	{
		$many = MetaData::getEntityRules('RelationshipLoader_ManyToMany1_Entity', new RepositoryContainer);
		$loader = $many['same2']['relationshipParam'];
		$this->assertInstanceOf('Orm\RelationshipLoader', $loader);
		$this->assertAttributeSame('RelationshipLoader_ManyToMany1_', 'repository', $loader);
		$this->assertAttributeSame('same2', 'param', $loader);
		$this->assertAttributeSame('same2', 'parentParam', $loader);
		$this->assertAttributeSame(true, 'mappedByThis', $loader);
	}

	public function testClearCache()
	{
		try {
			$this->t('manyAnotherRepo');
			throw new Exception;
		} catch (RelationshipLoaderException $e) {
			$this->setExpectedException('Orm\RelationshipLoaderException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherRepo neukazuje zpet; ukazuje na jiny repository (RelationshipLoader_ManyToMany3_)');
			$this->t('manyAnotherRepo');
		}
	}

	public function testIgnoreCase()
	{
		$ecn = & RelationshipLoader_ManyToMany2_Repository::$ecn;
		$old = $ecn;
		$ecn = strtoupper($ecn);
		$loader = $this->t('many');
		$this->assertInstanceOf('Orm\RelationshipLoader', $loader);
		$ecn = $old;
	}

	public function testMultiUse()
	{
		MetaData::clean();
		new RepositoryContainer;
		MetaData::getEntityRules('RelationshipLoader_ManyToMany4a_Entity');
		$this->assertTrue(true);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RelationshipLoader', 'check');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}
