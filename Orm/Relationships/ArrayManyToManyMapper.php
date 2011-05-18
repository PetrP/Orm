<?php

namespace Orm;

use Nette\Object;

require_once dirname(__FILE__) . '/IManyToManyMapper.php';

class ArrayManyToManyMapper extends Object implements IManyToManyMapper
{

	private $value;

	public function setValue($value)
	{
		$this->value = $value ? array_combine($value, $value) : array();
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setParams($parentIsFirst)
	{

	}

	public function add(IEntity $parent, array $ids)
	{
		$parent->isChanged(true);
		$this->value = $this->value + $ids;
	}

	public function remove(IEntity $parent, array $ids)
	{
		$parent->isChanged(true);
		$this->value = array_diff_key($this->value, $ids);
	}

	public function load(IEntity $parent)
	{
		return $this->value;
	}
}
