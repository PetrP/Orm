<?php

use Orm\Repository;
use Orm\OneToMany;


/**
 * @property OneToMany_persist_order_1_OneToMany $many {1:m OneToMany_persist_order_2_Repository $one}
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

class OneToMany_persist_order_1_OneToMany extends OneToMany
{
	public $orderProperty = false;

	protected function getOrderProperty()
	{
		if ($this->orderProperty !== false)
		{
			return $this->orderProperty;
		}
		return parent::getOrderProperty();
	}
}

/**
 * @property OneToMany_persist_order_1_Entity $one {m:1 OneToMany_persist_order_1_Repository $many}
 * @property int|NULL $order
 * @property int|NULL $order2
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
