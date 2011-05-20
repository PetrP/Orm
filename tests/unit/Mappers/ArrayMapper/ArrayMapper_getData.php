<?php

use Orm\ArrayMapper;

class ArrayMapper_getData_ArrayMapper extends ArrayMapper
{
	public $array = array(
		1 => array('id' => 1),
		2 => array('id' => 2),
	);

	protected function loadData()
	{
		return $this->array;
	}

	public function _getData()
	{
		return $this->getData();
	}
}
