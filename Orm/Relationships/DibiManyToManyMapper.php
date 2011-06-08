<?php

namespace Orm;

use Nette\Object;
use Nette\InvalidStateException;
use DibiConnection;

require_once __DIR__ . '/IManyToManyMapper.php';

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

	/** @return string */
	final protected function getParentParam()
	{
		return $this->parentIsFirst ? $this->firstParam : $this->secondParam;
	}

	/** @return string */
	final protected function getChildParam()
	{
		return $this->parentIsFirst ? $this->secondParam : $this->firstParam;
	}

	/** @param bool */
	public function setParams($parentIsFirst)
	{
		$this->parentIsFirst = (bool) $parentIsFirst;
		if (!$this->firstParam)
		{
			throw new InvalidStateException(get_class($this) . '::$firstParam is required');
		}
		if (!$this->secondParam)
		{
			throw new InvalidStateException(get_class($this) . '::$secondParam is required');
		}
		if (!$this->table)
		{
			throw new InvalidStateException(get_class($this) . '::$table is required');
		}
	}

	/**
	 * @param IEntity
	 * @param array id => id
	 */
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

	/**
	 * @param IEntity
	 * @param array id => id
	 */
	public function remove(IEntity $parent, array $ids)
	{
		$this->connection->delete($this->table)
			->where('%n = %s AND %n IN %in',
				$this->getParentParam(), $parent->id,
				$this->getChildParam(), $ids
			)->execute()
		;
	}

	/**
	 * @param IEntity
	 * @return array id => id
	 */
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
