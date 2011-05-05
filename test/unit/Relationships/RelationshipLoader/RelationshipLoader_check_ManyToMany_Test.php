<?php

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers RelationshipLoader::check
 * @covers MetaDataProperty::toArray
 * @covers RelationshipLoader::__construct
 */
class RelationshipLoader_check_ManyToMany_Test extends TestCase
{
	private function t($param)
	{
		MetaData::clean();
		new Model;
		RelationshipLoader_ManyToMany1_Entity::$param = $param;
		$many = MetaData::getEntityRules('RelationshipLoader_ManyToMany1_Entity');
		if (isset($many['manyX'])) return $many['manyX']['relationshipParam'];
		return $many['many']['relationshipParam'];
	}

	public function testUnexists()
	{
		$this->setExpectedException('InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_::$unexist neni asociace ktera by ukazovala zpet');
		$this->t('unexist');
	}

	public function testNotRelationship()
	{
		$this->setExpectedException('InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$id neni asociace ktera by ukazovala zpet');
		$this->t('id');
	}

	public function testParamEmpty()
	{
		$this->setExpectedException('InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyEmptyParam neni vyplnen param ktery by ukazoval zpet');
		$this->t('manyEmptyParam');
	}

	public function testAnotherParam()
	{
		$this->setExpectedException('InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherParam neukazuje zpet; ukazuje na jiny parametr (manyAnotherParam)');
		$this->t('manyAnotherParam');
	}

	public function testAnotherRepo()
	{
		$this->setExpectedException('InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherRepo neukazuje zpet; ukazuje na jiny repository (RelationshipLoader_ManyToMany3_)');
		$this->t('manyAnotherRepo');
	}

	public function testOk()
	{
		$loader = $this->t('many');
		$this->assertInstanceOf('RelationshipLoader', $loader);
	}

	public function testClearCache()
	{
		try {
			$this->t('manyAnotherRepo');
			throw new Exception;
		} catch (InvalidStateException $e) {
			$this->setExpectedException('InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherRepo neukazuje zpet; ukazuje na jiny repository (RelationshipLoader_ManyToMany3_)');
			$this->t('manyAnotherRepo');
		}
	}

}

/**
 * @property $manyAnotherParam {m:m RelationshipLoader_ManyToMany2_ manyAnotherParam}
 * @property $many {m:m RelationshipLoader_ManyToMany2_ many}
 */
class RelationshipLoader_ManyToMany1_Entity extends Entity
{
	static $param;
	public static function createMetaData($entityClass)
	{
		$meta = parent::createMetaData($entityClass);
		if (self::$param !== 'many')
		{
			$meta->addProperty('manyX', '')
				->setManyToMany('RelationshipLoader_ManyToMany2_', self::$param)
			;
		}
		return $meta;
	}
}

/**
 * @property $manyEmptyParam {m:m RelationshipLoader_ManyToMany1_}
 * @property $manyAnotherParam {m:m RelationshipLoader_ManyToMany1_ manyAnotherParam}
 * @property $manyAnotherRepo {m:m RelationshipLoader_ManyToMany3_ many}
 * @property $many {m:m RelationshipLoader_ManyToMany1_ many}
 */
class RelationshipLoader_ManyToMany2_Entity extends Entity
{

}

class RelationshipLoader_ManyToMany1_Repository extends Repository
{
	protected $entityClassName = 'RelationshipLoader_ManyToMany1_Entity';
}
class RelationshipLoader_ManyToMany1_Mapper extends TestsMapper
{
}
class RelationshipLoader_ManyToMany2_Repository extends Repository
{
	protected $entityClassName = 'RelationshipLoader_ManyToMany2_Entity';
}
class RelationshipLoader_ManyToMany2_Mapper extends TestsMapper
{
}

/**
 * @property $many {m:m RelationshipLoader_ManyToMany2_ manyAnotherRepo}
 */
class RelationshipLoader_ManyToMany3_Entity extends Entity
{

}

class RelationshipLoader_ManyToMany3_Repository extends Repository
{
	protected $entityClassName = 'RelationshipLoader_ManyToMany3_Entity';
}
class RelationshipLoader_ManyToMany3_Mapper extends TestsMapper
{
}
