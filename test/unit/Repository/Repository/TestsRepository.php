<?php

class TestsRepository extends Repository
{
	protected $entityClassName = 'TestEntity';
}

class TestsMapper extends ArrayMapper
{
	private $array = array(
		1 => array('id' => 1),
		2 => array('id' => 2),
	);

	protected function loadData()
	{
		return $this->array;
	}

	protected function saveData(array $data)
	{
		$this->array = $data;
	}
}
