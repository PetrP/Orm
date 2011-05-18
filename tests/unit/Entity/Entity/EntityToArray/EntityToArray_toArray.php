<?php

use Orm\Entity;
use Orm\Repository;
use Orm\ArrayMapper;
use Orm\OldManyToMany;

/**
 * @property TestEntity|NULL $e {m:1 TestEntity}
 */
class EntityToArray_toArray_m1_Entity extends Entity
{
}

/**
 * @property EntityToArray_ManyToMany $r {m:m}
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
class EntityToArray_ManyToMany extends OldManyToMany
{
	protected function getFirstRepository()
	{
		return $this->model->EntityToArray_toArray_1m_;
	}
	protected function getSecondRepository()
	{
		return $this->model->TestEntity;
	}
}
