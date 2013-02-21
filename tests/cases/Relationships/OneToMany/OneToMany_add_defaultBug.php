<?php

use Orm\Repository;

/**
 * @property Orm\OneToMany $many {1:m OneToMany_add_defaultBug_2_Repository $one}
 */
class OneToMany_add_defaultBug_1_Entity extends TestEntity
{
}

class OneToMany_add_defaultBug_1_Repository extends Repository
{
	protected $entityClassName = 'OneToMany_add_defaultBug_1_Entity';
}
class OneToMany_add_defaultBug_1_Mapper extends TestsMapper
{
	protected $array = array();
}

/**
 * @property OneToMany_add_defaultBug_1_Entity $one {m:1 OneToMany_add_defaultBug_1_Repository $many}
 */
class OneToMany_add_defaultBug_2_Entity extends TestEntity
{
	protected function getDefaultOne()
	{
		return 1;
	}
}

class OneToMany_add_defaultBug_2_Repository extends Repository
{
	protected $entityClassName = 'OneToMany_add_defaultBug_2_Entity';
}
class OneToMany_add_defaultBug_2_Mapper extends TestsMapper
{
	protected $array = array();
}
