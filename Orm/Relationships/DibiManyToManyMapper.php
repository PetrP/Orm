<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Object;
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
			throw new RequiredArgumentException(get_class($this) . '::$parentParam is required');
		}
		if (!$this->childParam)
		{
			throw new RequiredArgumentException(get_class($this) . '::$childParam is required');
		}
		if (!$this->table)
		{
			throw new RequiredArgumentException(get_class($this) . '::$table is required');
		}
		if (!$manyToMany->isMappedByParent())
		{
			$tmp = $this->childParam;
			$this->childParam = $this->parentParam;
			$this->parentParam = $tmp;
		}
	}

	/**
	 * @param IEntity
	 * @param array id => id
	 */
	public function add(IEntity $parent, array $ids)
	{
		if ($ids)
		{
			$f = $this->connection->command()->insert()
				->into('%n', $this->table, '(%n)', array($this->parentParam, $this->childParam))
			;
			$parentId = $parent->id;
			foreach ($ids as $childId)
			{
				$f->values(array($parentId, $childId));
			}
			$f->execute();
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
		throw new DeprecatedException(array($this, '$firstParam', $this, '$childParam'));
	}

	/** @deprecated */
	final public function getSecondParam()
	{
		throw new DeprecatedException(array($this, '$secondParam', $this, '$parentParam'));
	}

	/** @deprecated */
	final public function setFirstParam($v)
	{
		throw new DeprecatedException(array($this, '$firstParam', $this, '$childParam'));
	}

	/** @deprecated */
	final public function setSecondParam($v)
	{
		throw new DeprecatedException(array($this, '$secondParam', $this, '$parentParam'));
	}

	/** @deprecated */
	final public function setParams($parentIsFirst)
	{
		throw new DeprecatedException(array($this, 'setParams()', $this, 'attach()'));
	}

}
