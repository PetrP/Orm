<?php

use Orm\Entity;
use Orm\Repository;
use Orm\ArrayMapper;
use Orm\Injection;

/**
 * @property $mixed
 */
class ValueEntityFragment_clone_Entity extends Entity
{

}

class ValueEntityFragment_clone_Repository extends Repository
{
	protected $entityClassName = 'ValueEntityFragment_clone_Entity';
}

class ValueEntityFragment_clone_Mapper extends ArrayMapper
{

}

class ValueEntityFragment_clone_Injection extends Injection
{

}
