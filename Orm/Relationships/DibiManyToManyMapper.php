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

/**
 * Mapper for ManyToMany relationship.
 * It uses junction table.
 *
 * @see IMapper::createManyToManyMapper()
 * @see DibiMapper::createManyToManyMapper()
 */
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

	/** @var bool */
	private $both = false;

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
		if ($manyToMany->getWhereIsMapped() === RelationshipLoader::MAPPED_THERE)
		{
			$tmp = $this->childParam;
			$this->childParam = $this->parentParam;
			$this->parentParam = $tmp;
		}
		if ($manyToMany->getWhereIsMapped() === RelationshipLoader::MAPPED_BOTH)
		{
			$this->both = true;
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
		$sql = array('%n = %s AND %n IN %in',
			$this->parentParam, $parent->id,
			$this->childParam, $ids
		);
		if ($this->both)
		{
			$sql = array_merge(
				array('('), $sql, array(') OR (%n = %s AND %n IN %in)',
				$this->childParam, $parent->id,
				$this->parentParam, $ids
			));
		}
		$this->connection->delete($this->table)->where('%sql', $sql)->execute();
	}

	/**
	 * @param IEntity
	 * @return array id => id
	 */
	public function load(IEntity $parent)
	{
		if (!isset($parent->id)) return array();
		$result = $this->connection->select($this->childParam)
			->from($this->table)
			->where('%n = %s', $this->parentParam, $parent->id)
			->fetchPairs()
		;
		if ($this->both)
		{
			$result = array_unique(array_merge($result, $this->connection->select($this->parentParam)
				->from($this->table)
				->where('%n = %s', $this->childParam, $parent->id)
				->fetchPairs()
			));
		}
		return $result;
	}

	/** @return DibiConnection */
	final protected function getConnection()
	{
		return $this->connection;
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
		throw new DeprecatedException(get_class($this) . '::setParams() is deprecated; use ' . get_class($this) . '::attach() instead');
	}

}
