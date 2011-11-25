<?php

use Orm\Entity;
use Orm\Repository;
use Orm\ArrayMapper;

/**
 * @property TestEntity|NULL $e {m:1 TestEntity}
 */
class EntityToArray_toArray_m1_Entity extends Entity
{
}

/**
 * @property Orm\ManyToMany $r {m:m TestEntityRepository}
 */
class EntityToArray_toArray_1m_Entity extends Entity
{
}
class EntityToArray_toArray_1m_Repository extends Repository
{
	protected $entityClassName = 'EntityToArray_toArray_1m_Entity';
}
class EntityToArray_toArray_1m_Mapper extends ArrayMapper
{
}
