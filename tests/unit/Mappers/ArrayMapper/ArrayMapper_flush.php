<?php

use Orm\ArrayMapper;

class ArrayMapper_flush_saveData_ArrayMapper extends ArrayMapper
{
	private $count = 0;
	protected function loadData()
	{
		return array();
	}
	protected function saveData(array $data)
	{
		$this->count++;
		if ($this->count > 1)
		{
			parent::saveData($data);
		}
	}
}
