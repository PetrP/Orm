<?php

use Orm\Entity;
use Orm\Repository;

/**
 * @property $manyAnotherParam {m:m RelationshipMetaDataManyToMany_ManyToMany2_ manyAnotherParam}
 * @property $many {m:m RelationshipMetaDataManyToMany_ManyToMany2_ many mapped}
 * @property $same1 {m:m RelationshipMetaDataManyToMany_ManyToMany1_ same1 mapped}
 * @property $same2 {m:m RelationshipMetaDataManyToMany_ManyToMany1_ same2}
 */
class RelationshipMetaDataManyToMany_ManyToMany1_Entity extends Entity
{
	static $param;
	public static function createMetaData($entityClass)
	{
		$meta = parent::createMetaData($entityClass);
		if (self::$param !== 'many')
		{
			$meta->addProperty('manyX', '')
				->setManyToMany('RelationshipMetaDataManyToMany_ManyToMany2_', self::$param)
			;
		}
		return $meta;
	}
}

/**
 * @property $manyEmptyParam {m:m RelationshipMetaDataManyToMany_ManyToMany1_}
 * @property $manyAnotherParam {m:m RelationshipMetaDataManyToMany_ManyToMany1_ manyAnotherParam mapped}
 * @property $manyAnotherRepo {m:m RelationshipMetaDataManyToMany_ManyToMany3_ many mapped}
 * @property $many {m:m RelationshipMetaDataManyToMany_ManyToMany1_ many}
 */
class RelationshipMetaDataManyToMany_ManyToMany2_Entity extends Entity
{

}

class RelationshipMetaDataManyToMany_ManyToMany1_Repository extends Repository
{
	protected $entityClassName = 'RelationshipMetaDataManyToMany_ManyToMany1_Entity';
}
class RelationshipMetaDataManyToMany_ManyToMany1_Mapper extends TestsMapper
{
}
class RelationshipMetaDataManyToMany_ManyToMany2_Repository extends Repository
{
	public static $ecn = 'RelationshipMetaDataManyToMany_ManyToMany2_Entity';

	public function getEntityClassName(array $data = NULL)
	{
		return self::$ecn;
	}

}
class RelationshipMetaDataManyToMany_ManyToMany2_Mapper extends TestsMapper
{
}

/**
 * @property $many {m:m RelationshipMetaDataManyToMany_ManyToMany2_ manyAnotherRepo}
 */
class RelationshipMetaDataManyToMany_ManyToMany3_Entity extends Entity
{

}

class RelationshipMetaDataManyToMany_ManyToMany3_Repository extends Repository
{
	protected $entityClassName = 'RelationshipMetaDataManyToMany_ManyToMany3_Entity';
}
class RelationshipMetaDataManyToMany_ManyToMany3_Mapper extends TestsMapper
{
}


/**
 * @property $many {m:m RelationshipMetaDataManyToMany_ManyToMany5_ many mapped}
 */
class RelationshipMetaDataManyToMany_ManyToMany4a_Entity extends Entity
{

}

/**
 * @property $many {m:m RelationshipMetaDataManyToMany_ManyToMany5_ many mapped}
 */
class RelationshipMetaDataManyToMany_ManyToMany4b_Entity extends Entity
{

}

class RelationshipMetaDataManyToMany_ManyToMany4_Repository extends Repository
{
	public function getEntityClassName(array $data = NULL)
	{
		return array(
			'RelationshipMetaDataManyToMany_ManyToMany4a_Entity',
			'RelationshipMetaDataManyToMany_ManyToMany4b_Entity',
		);
	}
}
class RelationshipMetaDataManyToMany_ManyToMany4_Mapper extends TestsMapper
{
}

/**
 * @property $many {m:m RelationshipMetaDataManyToMany_ManyToMany4_ many}
 */
class RelationshipMetaDataManyToMany_ManyToMany5a_Entity extends Entity
{

}

/**
 * @property $many {m:m RelationshipMetaDataManyToMany_ManyToMany4_ many}
 */
class RelationshipMetaDataManyToMany_ManyToMany5b_Entity extends Entity
{

}

class RelationshipMetaDataManyToMany_ManyToMany5_Repository extends Repository
{
	public function getEntityClassName(array $data = NULL)
	{
		return array(
			'RelationshipMetaDataManyToMany_ManyToMany5a_Entity',
			'RelationshipMetaDataManyToMany_ManyToMany5b_Entity',
		);
	}
}
class RelationshipMetaDataManyToMany_ManyToMany5_Mapper extends TestsMapper
{
}

/**
 * @property $many {m:m RelationshipMetaDataManyToMany_ManyToMany6_Repository many}
 */
class RelationshipMetaDataManyToMany_ManyToMany6_Entity extends Entity
{

}
class RelationshipMetaDataManyToMany_ManyToMany6_Repository extends Repository
{
	protected $entityClassName = 'RelationshipMetaDataManyToMany_ManyToMany6_Entity';
}
class RelationshipMetaDataManyToMany_ManyToMany6_Mapper extends TestsMapper
{
}
