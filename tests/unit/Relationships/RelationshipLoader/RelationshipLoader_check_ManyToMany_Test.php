<?php

use Nette\InvalidStateException;
use Orm\MetaData;
use Orm\Entity;
use Orm\Repository;
use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\RelationshipLoader::check
 * @covers Orm\MetaDataProperty::toArray
 * @covers Orm\RelationshipLoader::__construct
 */
class RelationshipLoader_check_ManyToMany_Test extends TestCase
{
	private function t($param)
	{
		MetaData::clean();
		new RepositoryContainer;
		RelationshipLoader_ManyToMany1_Entity::$param = $param;
		$many = MetaData::getEntityRules('RelationshipLoader_ManyToMany1_Entity');
		if (isset($many['manyX'])) return $many['manyX']['relationshipParam'];
		return $many['many']['relationshipParam'];
	}

	public function testUnexists()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_::$unexist neni asociace ktera by ukazovala zpet');
		$this->t('unexist');
	}

	public function testNotRelationship()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$id neni asociace ktera by ukazovala zpet');
		$this->t('id');
	}

	public function testParamEmpty()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyEmptyParam neni vyplnen param ktery by ukazoval zpet');
		$this->t('manyEmptyParam');
	}

	public function testAnotherParam()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherParam neukazuje zpet; ukazuje na jiny parametr (manyAnotherParam)');
		$this->t('manyAnotherParam');
	}

	public function testAnotherRepo()
	{
		$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherRepo neukazuje zpet; ukazuje na jiny repository (RelationshipLoader_ManyToMany3_)');
		$this->t('manyAnotherRepo');
	}

	public function testOk()
	{
		$loader = $this->t('many');
		$this->assertInstanceOf('Orm\RelationshipLoader', $loader);
	}

	public function testClearCache()
	{
		try {
			$this->t('manyAnotherRepo');
			throw new Exception;
		} catch (InvalidStateException $e) {
			$this->setExpectedException('Nette\InvalidStateException', 'RelationshipLoader_ManyToMany1_Entity::$manyX {m:m} na druhe strane asociace RelationshipLoader_ManyToMany2_Entity::$manyAnotherRepo neukazuje zpet; ukazuje na jiny repository (RelationshipLoader_ManyToMany3_)');
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

}

/**
 * @property $manyAnotherParam {m:m RelationshipLoader_ManyToMany2_ manyAnotherParam}
 * @property $many {m:m RelationshipLoader_ManyToMany2_ many mapped}
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
 * @property $manyAnotherParam {m:m RelationshipLoader_ManyToMany1_ manyAnotherParam mapped}
 * @property $manyAnotherRepo {m:m RelationshipLoader_ManyToMany3_ many mapped}
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
	public static $ecn = 'RelationshipLoader_ManyToMany2_Entity';

	public function getEntityClassName(array $data = NULL)
	{
		return self::$ecn;
	}

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


/**
 * @property $many {m:m RelationshipLoader_ManyToMany5_ many mapped}
 */
class RelationshipLoader_ManyToMany4a_Entity extends Entity
{

}

/**
 * @property $many {m:m RelationshipLoader_ManyToMany5_ many mapped}
 */
class RelationshipLoader_ManyToMany4b_Entity extends Entity
{

}

class RelationshipLoader_ManyToMany4_Repository extends Repository
{
	public function getEntityClassName(array $data = NULL)
	{
		return array(
			'RelationshipLoader_ManyToMany4a_Entity',
			'RelationshipLoader_ManyToMany4b_Entity',
		);
	}
}
class RelationshipLoader_ManyToMany4_Mapper extends TestsMapper
{
}

/**
 * @property $many {m:m RelationshipLoader_ManyToMany4_ many}
 */
class RelationshipLoader_ManyToMany5a_Entity extends Entity
{

}

/**
 * @property $many {m:m RelationshipLoader_ManyToMany4_ many}
 */
class RelationshipLoader_ManyToMany5b_Entity extends Entity
{

}

class RelationshipLoader_ManyToMany5_Repository extends Repository
{
	public function getEntityClassName(array $data = NULL)
	{
		return array(
			'RelationshipLoader_ManyToMany5a_Entity',
			'RelationshipLoader_ManyToMany5b_Entity',
		);
	}
}
class RelationshipLoader_ManyToMany5_Mapper extends TestsMapper
{
}
