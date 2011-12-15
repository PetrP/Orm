<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use DibiConnection;

/**
 * Mapper for ManyToMany relationship.
 * It uses junction table.
 *
 * @see IMapper::createManyToManyMapper()
 * @see DibiMapper::createManyToManyMapper()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\Mappers
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

	/** @var mixed RelationshipMetaDataToMany::MAPPED_* */
	private $mapped;

	/** @param DibiConnection */
	public function __construct(DibiConnection $connection)
	{
		$this->connection = $connection;
	}

	/** @param RelationshipMetaDataManyToMany */
	public function attach(RelationshipMetaDataManyToMany $meta)
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
		$this->mapped = $meta->getWhereIsMapped();
		if ($this->mapped === RelationshipMetaDataToMany::MAPPED_THERE)
		{
			$tmp = $this->childParam;
			$this->childParam = $this->parentParam;
			$this->parentParam = $tmp;
		}
	}

	/**
	 * @param IEntity
	 * @param array id => id
	 * @param NULL
	 * @return NULL
	 */
	public function add(IEntity $parent, array $ids, $injectedValue)
	{
		if ($this->mapped === RelationshipMetaDataToMany::MAPPED_THERE)
		{
			throw new NotSupportedException('Orm\IManyToManyMapper::add() has not supported on inverse side.');
		}

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
	 * @param NULL
	 * @return NULL
	 */
	public function remove(IEntity $parent, array $ids, $injectedValue)
	{
		if ($this->mapped === RelationshipMetaDataToMany::MAPPED_THERE)
		{
			throw new NotSupportedException('Orm\IManyToManyMapper::remove() has not supported on inverse side.');
		}

		$sql = array('%n = %s AND %n IN %in',
			$this->parentParam, $parent->id,
			$this->childParam, $ids
		);
		if ($this->mapped === RelationshipMetaDataToMany::MAPPED_BOTH)
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
	 * @param NULL
	 * @return array id => id
	 */
	public function load(IEntity $parent, $injectedValue)
	{
		if (!isset($parent->id)) return array();
		$result = $this->connection->select($this->childParam)
			->from($this->table)
			->where('%n = %s', $this->parentParam, $parent->id)
			->fetchPairs()
		;
		if ($this->mapped === RelationshipMetaDataToMany::MAPPED_BOTH)
		{
			$result = array_unique(array_merge($result, $this->connection->select($this->parentParam)
				->from($this->table)
				->where('%n = %s', $this->childParam, $parent->id)
				->fetchPairs()
			));
		}
		return $result;
	}

	/**
	 * @param mixed {@see ManyToMany::$injectedValue}
	 * @return NULL will be set as {@see ManyToMany::$injectedValue}
	 */
	public function validateInjectedValue($injectedValue)
	{
		return NULL;
	}

	/** @return DibiConnection */
	final protected function getConnection()
	{
		return $this->connection;
	}

	/** @return mixed RelationshipMetaDataToMany::MAPPED_* */
	final protected function getWhereIsMapped()
	{
		return $this->mapped;
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
