<?php

class Mapper_call_Collection extends Mapper_getCollectionClass_OtherCollection
{
	static $last;
	public function __call($name, $args)
	{
		self::$last = func_get_args();
		return 'xyz';
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
