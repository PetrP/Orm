<?php

namespace Orm;

use Nette\NotImplementedException;
use DibiConnection;

require_once dirname(__FILE__) . '/IEntityCollection.php';
require_once dirname(__FILE__) . '/BaseDibiCollection.php';

class DibiCollection extends BaseDibiCollection implements IEntityCollection
{

	/** @var string */
	protected $tableAlias = 'e.';

	/** @var string */
	private $tableName;

	/** @var array @see self::join() */
	private $join = array();

	/** @var int cache */
	private $count;

	/**
	 * @param string
	 * @param DibiConnection
	 * @param IRepository
	 */
	final public function __construct($tableName, DibiConnection $connection, IRepository $repository)
	{
		$this->tableName = $tableName;
		parent::__construct($connection, $repository);
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 * @todo optimalozovat
	 */
	final public function count()
	{
		if ($this->count === NULL)
		{
			$this->count = $this->getResult()->count();
		}
		return $this->count;
	}

	/**
	 * Pripoji asociaci.
	 * @see DibiMapper::getJoinInfo()
	 * @param string
	 * @return DibiCollection $this
	 * @todo public?
	 */
	final public function join($key)
	{
		$lastAlias = 'e';
		foreach ($this->repository->getMapper()->getJoinInfo($key)->joins as $join)
		{
			if (!isset($this->join[$join['alias']]))
			{
				$this->join[$join['alias']] = array(
					"LEFT JOIN %n as %n ON %n = %n",
					$join['table'], $join['alias'], $join['alias'] . '.' . $join['yConventionalKey'], $lastAlias . '.' . $join['xConventionalKey']
				);
				if ($join['findBy'])
				{
					$findBy = $join['findBy'];
					$where = array();
					FindByHelper::dibiProcess(
						$this,
						$this->repository->getMapper(),
						$this->conventional,
						$where,
						$findBy,
						$this->tableAlias,
						$join['alias']
					);
					$this->join[$join['alias']][] = 'AND %and';
					$this->join[$join['alias']][] = $where;
				}
			}
			$lastAlias = $join['alias'];
		}
		return $this;
	}

	/** @return DataSourceCollection */
	final public function toDataSourceCollection()
	{
		return new DataSourceCollection($this->__toString(), $this->connection, $this->repository);
	}

	/**
	 * Returns sql query
	 * @return string
	 */
	final public function __toString()
	{
		list($sorting, $limit, $offset) = $this->process();
		$orderBy = array();
		end($sorting); $end = key($sorting);
		foreach ($sorting as $i => $tmp)
		{
			list($key, $direction) = $tmp;
			$orderBy[] = '%by' . ($end === $i ? '' : ', ');
			$orderBy[] = array($key => $direction);
		}
		FindByHelper::dibiProcess(
			$this,
			$this->repository->getMapper(),
			$this->conventional,
			$this->where,
			$this->findBy,
			$this->tableAlias
		);

		$join = array();
		foreach ($this->join as $tmp) $join = array_merge($join, $tmp);

		return $this->connectionTranslate('
			SELECT [e.*]
			FROM %n', $this->tableName, ' as e
			%ex', $join,'
			%ex', $this->where ? array('WHERE %and', $this->where) : NULL, '
			' . ($join ? 'GROUP BY [e.id]' : '') . '
			%ex', $orderBy ? array('ORDER BY %sql', $orderBy) : NULL, '
			%ofs %lmt', $offset, $limit
		);
	}

	/**
	 * Discards the internal cache.
	 * @param bool
	 */
	protected function release($count = false)
	{
		if ($count)
		{
			$this->count = NULL;
		}
		parent::release();
	}

	/**
	 * Use DibiConnection::translate() or DibiConnection::sql()
	 * @return string sql
	 */
	final private function connectionTranslate($args)
	{
		static $translate;
		if ($translate === NULL)
		{
			$translate = method_exists($this->connection, 'translate') ? 'translate' : 'sql';
		}
		$args = func_get_args();
		return $this->connection->$translate($args);
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	final public function getTotalCount()
	{
		throw new NotImplementedException();
	}

}
