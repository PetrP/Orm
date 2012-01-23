<?php

use Orm\Entity;
use Orm\Repository;
use Orm\DibiMapper;
use Orm\ArrayMapper;

/**
 * @property string $name
 * @property ArrayCollection_join2_Entity $join2 {m:1 ArrayCollection_join2_}
 * @property ArrayCollection_join3_Entity $join3 {m:1 ArrayCollection_join3_}
 * @property ArrayCollection_join4_Entity $join4 {m:1 ArrayCollection_join4_}
 * @property ArrayCollection_join2_Entity|NULL $joinNull {m:1 ArrayCollection_join2_}
 */
class ArrayCollection_join1_Entity extends Entity
{

}

/**
 * @property string $name
 * @property ArrayCollection_join1_Entity $join1 {m:1 ArrayCollection_join1_}
 */
class ArrayCollection_join2_Entity extends Entity
{

}

/** @mapper ArrayCollection_join_Mapper */
class ArrayCollection_join1_Repository extends Repository
{
	protected $entityClassName = 'ArrayCollection_join1_Entity';
}

class ArrayCollection_join2_Repository extends ArrayCollection_join1_Repository
{
	protected $entityClassName = 'ArrayCollection_join2_Entity';
}

class ArrayCollection_join_Mapper extends TestsMapper
{
	protected $array = array(
		array('id' => 1, 'name' => 'a'),
		array('id' => 2, 'name' => 'b'),
		array('id' => 3, 'name' => 'c'),
		array('id' => 4, 'name' => 'd'),
		array('id' => 5, 'name' => 'e'),
	);
}

class ArrayCollection_join3_Entity extends ArrayCollection_join2_Entity
{

}
class ArrayCollection_join4_Entity extends ArrayCollection_join2_Entity
{

}

class ArrayCollection_join3_Repository extends Repository
{
	protected $entityClassName = 'ArrayCollection_join3_Entity';
}

class ArrayCollection_join3_Mapper extends ArrayCollection_join_Mapper
{
	public function findAll()
	{
		return parent::findAll()->findById(array(3, 4, 5));
	}
}


class ArrayCollection_join4_Repository extends Repository
{
	protected $entityClassName = 'ArrayCollection_join4_Entity';
}

class ArrayCollection_join4_Mapper extends ArrayCollection_join_Mapper
{

	protected $array = array(
		array('id' => 1, 'name' => 'a', 'join1' => 1),
		array('id' => 2, 'name' => 'b', 'join1' => 2),
		array('id' => 3, 'name' => 'c', 'join1' => 3),
		array('id' => 4, 'name' => 'd', 'join1' => 4),
		array('id' => 5, 'name' => 'e', 'join1' => 5),
	);

	public function findAll()
	{
		return parent::findAll()
			->findBy(array('join1->join2->id' => array(1, 2, 3))) // 2, 3, 5
			->findBy(array('join1->id' => array(1, 2, 5))) // 2, 5
		;
	}
}
