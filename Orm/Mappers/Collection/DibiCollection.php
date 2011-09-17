<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use DibiConnection;

/**
 * Collection of entities, represented as basic sql.
 *
 * <code>
 * $collection = $dibiMapper->findAll()->where('[foo] = %s', $foo);
 * </code>
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection
 */
class DibiCollection extends BaseDibiCollection
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
		foreach ($this->getMapper()->getJoinInfo($key)->joins as $join)
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
						$this->getMapper(),
						$this->getConventional(),
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
		return new DataSourceCollection($this->__toString(), $this->getConnection(), $this->getRepository());
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
			$this->getMapper(),
			$this->getConventional(),
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
			// @codeCoverageIgnoreStart
			$translate = method_exists($this->getConnection(), 'translate') ? 'translate' : 'sql';
		}	// @codeCoverageIgnoreEnd
		$args = func_get_args();
		return $this->getConnection()->$translate($args);
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
