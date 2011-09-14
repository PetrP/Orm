<?php

use Orm\Object;

class Object_Object extends Object
{
	public function getBar()
	{
		return 3;
	}

	public function isBool()
	{
		return 4;
	}

	public function setSetter($v)
	{
		throw new Exception($v);
	}

	public function setSetter2($v)
	{
		return $v;
	}

}
