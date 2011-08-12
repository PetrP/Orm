<?php

use Orm\Entity;
use Orm\Repository;

/**
 * @property Orm\ManyToMany $r {m:m EntityToArray_toArray_recursiton_mm_}
 * @property string $string
 */
class EntityToArray_toArray_recursiton_mm_Entity extends Entity
{
}
class EntityToArray_toArray_recursiton_mm_Repository extends Repository
{
	protected $entityClassName = 'EntityToArray_toArray_recursiton_mm_Entity';
}
class EntityToArray_toArray_recursiton_mm_Mapper extends TestEntityMapper
{
}

/**
 * @property EntityToArray_toArray_recursiton_1m_Entity $a {m:1 EntityToArray_toArray_recursiton_1m_}
 * @property Orm\OneToMany $b {1:m EntityToArray_toArray_recursiton_1m_ a}
 * @property string $string
 */
class EntityToArray_toArray_recursiton_1m_Entity extends Entity
{
}
class EntityToArray_toArray_recursiton_1m_Repository extends Repository
{
	protected $entityClassName = 'EntityToArray_toArray_recursiton_1m_Entity';
}
class EntityToArray_toArray_recursiton_1m_Mapper extends TestEntityMapper
{
}
