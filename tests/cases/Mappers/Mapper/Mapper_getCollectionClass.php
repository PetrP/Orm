<?php

use Orm\Object;
use Orm\IEntityCollection;
use Orm\DibiCollection;
use Orm\DataSourceCollection;
use Orm\ArrayCollection;
use Orm\ArrayMapper;

abstract class Mapper_getCollectionClass_Collection extends Object implements IEntityCollection
{

}

class Mapper_getCollectionClass_DibiCollection extends DibiCollection {}
class Mapper_getCollectionClass_DataSourceCollection extends DataSourceCollection {}
class Mapper_getCollectionClass_ArrayCollection extends ArrayCollection {}

class Mapper_getCollectionClass_OtherCollection extends Mapper_getCollectionClass_Collection
{
	public function orderBy($row, $direction = Dibi::ASC){}
	public function applyLimit($limit, $offset = NULL){}
	public function fetch(){}
	public function fetchAll(){}
	public function fetchAssoc($assoc){}
	public function fetchPairs($key = NULL, $value = NULL){}
	public function toArrayCollection(){}
	public function toCollection(){}
	public function count(){}
	public function getIterator(){}
	public function findBy(array $where){}
	public function getBy(array $where){}
}


class Mapper_getCollectionClass_Mapper extends ArrayMapper
{
	public function mockGetCollectionClass()
	{
		if (func_num_args() > 0)
		{
			return $this->getCollectionClass(func_get_arg(0));
		}
		return $this->getCollectionClass();
	}

	public $cc;
	protected function createCollectionClass()
	{
		if ($this->cc) return $this->cc;
		return parent::createCollectionClass();
	}
}

class Mapper_getCollectionClass_BadCollection extends Mapper_getCollectionClass_OtherCollection
{
	protected function __construct()
	{

	}
}
