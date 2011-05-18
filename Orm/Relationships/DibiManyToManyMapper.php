<?php

namespace Orm;


require_once dirname(__FILE__) . '/IManyToManyMapper.php';

class DibiManyToManyMapper extends Object implements IManyToManyMapper
{
	/** @var string */
	public $table;

	/** @var string */
	public $firstParam;

	/** @var string */
	public $secondParam;

	/** @var DibiConnection */
	private $connection;

	/** @var bool */
	private $parentIsFirst;


	/** @param DibiConnection */
	public function __construct(DibiConnection $connection)
	{
		$this->connection = $connection;
	}

	final protected function getParentParam()
	{
		return $this->parentIsFirst ? $this->firstParam : $this->secondParam;
	}

	final protected function getChildParam()
	{
		return $this->parentIsFirst ? $this->secondParam : $this->firstParam;
	}

	public function setParams($parentIsFirst)
	{
		$this->parentIsFirst = $parentIsFirst;
		if (!$this->firstParam OR !$this->secondParam OR !$this->table) throw new Exception();
	}

	public function add(IEntity $parent, array $ids)
	{
		$parentId = $parent->id;
		$parentParam = $this->getParentParam();
		$childParam = $this->getChildParam();
		foreach ($ids as $childId)
		{
			// todo jeden dotaz
			$this->connection->insert($this->table, array(
				$parentParam => $parentId,
				$childParam => $childId,
			))->execute();
		}
	}

	public function remove(IEntity $parent, array $ids)
	{
		$this->connection->delete($this->table)
			->where('%n = %s AND %n IN %in',
				$this->getParentParam(), $parent->id,
				$this->getChildParam(), $ids
			)->execute()
		;
	}

	public function load(IEntity $parent)
	{
		if (!isset($parent->id)) return array();
		return $this->connection->select($this->getChildParam())
			->from($this->table)
			->where('%n = %s',
				$this->getParentParam(), $parent->id
			)->fetchPairs()
		;
	}
}
