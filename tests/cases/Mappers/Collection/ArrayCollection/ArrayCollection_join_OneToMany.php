<?php

use Orm\Entity;
use Orm\Repository;

/**
 * @property string $name
 * @property $joins {1:m ArrayCollection_join_OneToMany2_ $join}
 * @property $joinsNull {1:m ArrayCollection_join_OneToMany2_ $joinNull}
 * @property ArrayCollection_join_OneToMany2_Entity $join {m:1 ArrayCollection_join_OneToMany2_ $joins}
 */
class ArrayCollection_join_OneToMany1_Entity extends Entity
{

}

/**
 * @property string $name
 * @property ArrayCollection_join_OneToMany1_Entity $join {m:1 ArrayCollection_join_OneToMany1_ $joins}
 * @property ArrayCollection_join_OneToMany1_Entity|NULL $joinNull {m:1 ArrayCollection_join_OneToMany1_ $joinsNull}
 * @property $joins {1:m ArrayCollection_join_OneToMany1_ $join}
 */
class ArrayCollection_join_OneToMany2_Entity extends Entity
{

}

/** @mapper ArrayCollection_join_OneToMany_Mapper */
class ArrayCollection_join_OneToMany1_Repository extends Repository
{
	protected $entityClassName = 'ArrayCollection_join_OneToMany1_Entity';
}

class ArrayCollection_join_OneToMany2_Repository extends ArrayCollection_join_OneToMany1_Repository
{
	protected $entityClassName = 'ArrayCollection_join_OneToMany2_Entity';
}

class ArrayCollection_join_OneToMany_Mapper extends ArrayCollection_join_Mapper
{
}
