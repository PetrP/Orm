<?php

use Orm\Entity;
use Orm\Repository;
use Orm\IRepository;

/**
 * @property string $name
 * @property $joins {m:m ArrayCollection_join_ManyToMany2_ $joins map}
 * @property $joinsNull {m:m ArrayCollection_join_ManyToMany2_}
 */
class ArrayCollection_join_ManyToMany1_Entity extends Entity
{

}

/**
 * @property string $name
 * @property $joins {m:m ArrayCollection_join_ManyToMany1_ $joins}
 */
class ArrayCollection_join_ManyToMany2_Entity extends Entity
{

}

/** @mapper ArrayCollection_join_ManyToMany_Mapper */
class ArrayCollection_join_ManyToMany1_Repository extends Repository
{
	protected $entityClassName = 'ArrayCollection_join_ManyToMany1_Entity';
}

class ArrayCollection_join_ManyToMany2_Repository extends ArrayCollection_join_ManyToMany1_Repository
{
	protected $entityClassName = 'ArrayCollection_join_ManyToMany2_Entity';
}

class ArrayCollection_join_ManyToMany_Mapper extends ArrayCollection_join_Mapper
{

}
