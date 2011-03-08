<?php

require_once dirname(__FILE__) . '/IManyToManyMapper.php';

class ArrayManyToManyMapper extends Object implements IManyToManyMapper
{
	public $value;

	public function setParams($parentIsFirst, IRepository $firstRepository, IRepository $secondRepository)
	{

	}

	public function add(IEntity $parent, array $ids)
	{
		$this->value = $this->value + $ids;
	}

	public function remove(IEntity $parent, array $ids)
	{
		$this->value = array_diff_key($this->value, $ids);
	}

	public function load(IEntity $parent)
	{
		return $this->value;
	}
}
