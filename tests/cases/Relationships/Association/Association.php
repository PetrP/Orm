<?php

use Orm\Entity;
use Orm\Repository;

/**
 * @property Association_Entity|NULL $manyToOne {m:1 Association_Repository $oneToMany}
 * @property Orm\OneToMany $oneToMany {1:m Association_Repository $manyToOne}
 * @property Association_Entity $manyToOneNotNull {m:1 Association_Repository $oneToManyNotNull}
 * @property Orm\OneToMany $oneToManyNotNull {1:m Association_Repository $manyToOneNotNull}
 *
 * @property Orm\ManyToMany $manyToMany1 {m:m Association_Repository $manyToMany2 mapped}
 * @property Orm\ManyToMany $manyToMany2 {m:m Association_Repository $manyToMany1}
 * @property Orm\ManyToMany $manyToManySame {m:m Association_Repository $manyToManySame}
 */
class Association_Entity extends Entity
{
}
class Association_Repository extends Repository
{
	protected $entityClassName = 'Association_Entity';
}
class Association_Mapper extends TestsMapper
{
	protected $array = array(
		1 => array('id' => 1),
		2 => array('id' => 2),
		3 => array('id' => 3),
		4 => array('id' => 4),
		5 => array('id' => 5),
	);
}
