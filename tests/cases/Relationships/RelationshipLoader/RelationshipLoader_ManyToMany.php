<?php

use Orm\Entity;
use Orm\Repository;

/**
 * @property $manyAnotherParam {m:m RelationshipLoader_ManyToMany2_ manyAnotherParam}
 * @property $many {m:m RelationshipLoader_ManyToMany2_ many mapped}
 * @property $same1 {m:m RelationshipLoader_ManyToMany1_ same1 mapped}
 * @property $same2 {m:m RelationshipLoader_ManyToMany1_ same2}
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
