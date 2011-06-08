<?php

namespace Orm;

use Nette\Object;

require_once __DIR__ . '/IManyToManyMapper.php';

class ArrayManyToManyMapper extends Object implements IManyToManyMapper
{
	/** @var array id => id */
	private $value;

	/** @param array of id */
	public function setValue($value)
	{
		$this->value = $value ? array_combine($value, $value) : array();
	}

	/** @return array id => id */
	public function getValue()
	{
		return $this->value;
	}

	/** @param bool */
	public function setParams($parentIsFirst)
	{

	}

	/**
	 * @param IEntity
	 * @param array id => id
	 */
	public function add(IEntity $parent, array $ids)
	{
		$parent->isChanged(true);
		$this->value = $this->value + $ids;
	}

	/**
	 * @param IEntity
	 * @param array id => id
	 */
	public function remove(IEntity $parent, array $ids)
	{
		$parent->isChanged(true);
		$this->value = array_diff_key($this->value, $ids);
	}

	/**
	 * @param IEntity
	 * @return array id => id
	 */
	public function load(IEntity $parent)
	{
		return $this->value;
	}
}
