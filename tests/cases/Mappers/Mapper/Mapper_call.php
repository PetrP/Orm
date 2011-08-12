<?php

use Orm\ArrayMapper;

class Mapper_call_Collection extends Mapper_getCollectionClass_OtherCollection
{
	static $last;
	public function __call($name, $args)
	{
		self::$last = func_get_args();
		return 'xyz';
	}
	public function findBy(array $where)
	{
		self::$last = array('findBy', $where);
		return 'qwe';
	}
	public function getBy(array $where)
	{
		self::$last = array('getBy', $where);
		return 'zxc';
	}
}

class Mapper_call_Mapper extends ArrayMapper
{
	public function getByXyz()
	{
		return 'getByXyz';
	}

	protected function getByProtected()
	{
		return 'getByProtected';
	}

	protected function createCollectionClass()
	{
		return 'Mapper_call_Collection';
	}
}
