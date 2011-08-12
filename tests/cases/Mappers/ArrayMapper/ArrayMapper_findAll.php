<?php

use Orm\ArrayMapper;

class ArrayMapper_findAll_ArrayMapper extends ArrayMapper
{
	public $array = array(
		1 => array('id' => 1),
		2 => array('id' => 2),
	);

	protected function loadData()
	{
		return $this->array;
	}
}
