<?php

use Orm\Repository;

/**
 * @property Orm\OneToMany $many {1:m OneToMany_persist_order_2_Repository $one}
 */
class OneToMany_persist_order_1_Entity extends TestEntity
{
}

class OneToMany_persist_order_1_Repository extends Repository
{
	protected $entityClassName = 'OneToMany_persist_order_1_Entity';
}
class OneToMany_persist_order_1_Mapper extends TestsMapper
{
	protected $array = array();
}

/**
 * @property OneToMany_persist_order_1_Entity $one {m:1 OneToMany_persist_order_1_Repository $many}
 * @property int $order
 */
class OneToMany_persist_order_2_Entity extends TestEntity
{
}

class OneToMany_persist_order_2_Repository extends Repository
{
	protected $entityClassName = 'OneToMany_persist_order_2_Entity';
}
class OneToMany_persist_order_2_Mapper extends TestsMapper
{
	protected $array = array();
}
