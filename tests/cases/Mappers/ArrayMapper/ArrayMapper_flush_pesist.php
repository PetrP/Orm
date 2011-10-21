<?php

use Orm\Injection;

/**
 * @property mixed $mixed
 * @property mixed $miXed
 */
class ArrayMapper_flush_Entity extends TestEntity
{

}

class ArrayMapper_flush_Repository extends TestsRepository
{
	protected $entityClassName = 'ArrayMapper_flush_Entity';
}

class ArrayMapper_flush_Mapper extends TestsMapper
{
	public $conv;
	protected function createConventional()
	{
		if ($this->conv) return $this->conv;
		return parent::createConventional();
	}
}

class ArrayMapper_flush_Injection extends Injection
{

}

class MyArrayObject extends ArrayObject
{

}
