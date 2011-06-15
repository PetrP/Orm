<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Object;
use Nette\InvalidStateException;
use Nette\DeprecatedException;
use DibiConnection;

require_once __DIR__ . '/IManyToManyMapper.php';

class DibiManyToManyMapper extends Object implements IManyToManyMapper
{
	/** @var string */
	public $table;

	/** @var string */
	public $parentParam;

	/** @var string */
	public $childParam;

	/** @var DibiConnection */
	private $connection;

	/** @param DibiConnection */
	public function __construct(DibiConnection $connection)
	{
		$this->connection = $connection;
	}

	/** @param ManyToMany */
	public function attach(ManyToMany $manyToMany)
	{
		if (!$this->parentParam)
		{
			throw new InvalidStateException(get_class($this) . '::$parentParam is required');
		}
		if (!$this->childParam)
		{
			throw new InvalidStateException(get_class($this) . '::$childParam is required');
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
		foreach ($ids as $childId)
		{
			// todo jeden dotaz
			$this->connection->insert($this->table, array(
				$this->parentParam => $parentId,
				$this->childParam => $childId,
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
				$this->parentParam, $parent->id,
				$this->childParam, $ids
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
		return $this->connection->select($this->childParam)
			->from($this->table)
			->where('%n = %s',
				$this->parentParam, $parent->id
			)->fetchPairs()
		;
	}

	/** @deprecated */
	final public function getFirstParam()
	{
		throw new DeprecatedException(get_class($this) . '::$firstParam is deprecated; use ' . get_class($this) . '::$childParam instead');
	}

	/** @deprecated */
	final public function getSecondParam()
	{
		throw new DeprecatedException(get_class($this) . '::$secondParam is deprecated; use ' . get_class($this) . '::$parentParam instead');
	}

	/** @deprecated */
	final public function setFirstParam($v)
	{
		throw new DeprecatedException(get_class($this) . '::$firstParam is deprecated; use ' . get_class($this) . '::$childParam instead');
	}

	/** @deprecated */
	final public function setSecondParam($v)
	{
		throw new DeprecatedException(get_class($this) . '::$secondParam is deprecated; use ' . get_class($this) . '::$parentParam instead');
	}

	/** @deprecated */
	final public function setParams($parentIsFirst)
	{
		throw new DeprecatedException;
	}

}
