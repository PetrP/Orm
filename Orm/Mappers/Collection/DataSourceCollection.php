<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use DibiDataSourceX;
use DibiDataSource;
use DibiConnection;

/**
 * Collection of entities, represented as complex sql.
 * @see DibiDataSource with subselects
 * @see DibiDataSourceX without subselects
 *
 * For mysql is prefer DibiDataSourceX which works without subselects.
 *
 * <code>
 * $collection = $dibiMapper->dataSource('SELECT * FROM [tableName] WHERE [foo] = %s', $foo);
 * </code>
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection
 */
class DataSourceCollection extends BaseDibiCollection
{

	/** @var string */
	private $sql;

	/** @var int */
	private $count;

	/** @var DibiDataSource */
	private $dataSource;

	/**
	 * @param string
	 * @param DibiConnection
	 * @param IRepository
	 */
	final public function __construct($sql, DibiConnection $connection, IRepository $repository)
	{
		$this->sql = $sql;
		parent::__construct($connection, $repository);
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	final public function count()
	{
		if ($this->count === NULL)
		{
			$this->count = $this->getDataSource()->count();
		}
		return $this->count;
	}

	/**
	 * Pripoji asociaci.
	 * @see DibiMapper::getJoinInfo()
	 * @param string
	 * @return DataSourceCollection $this
	 */
	public function join($key)
	{
		throw new NotSupportedException('Joins are not supported for DataSourceCollection');
	}

	/**
	 * Returns SQL query.
	 * @return string
	 */
	final public function __toString()
	{
		return $this->getDataSource()->__toString();
	}

	/** @return DibiDataSource */
	final public function getDataSource()
	{
		if ($this->dataSource === NULL)
		{
			list($sorting, $limit, $offset) = $this->process();
			FindByHelper::dibiProcess(
				$this,
				$this->getMapper(),
				$this->getConventional(),
				$this->where,
				$this->findBy,
				$this->tableAlias
			);

			static $dsClass;
			if ($dsClass === NULL)
			{
				// @codeCoverageIgnoreStart
				$dsClass = 'DibiDataSource';
				if (class_exists('DibiDataSourceX'))
				{
					$dsClass = 'DibiDataSourceX';
				}
			}	// @codeCoverageIgnoreEnd

			$ds = new $dsClass($this->sql, $this->getConnection());

			foreach ($this->where as $where)
			{
				$ds->where($where);
			}

			foreach ($sorting as $tmp)
			{
				list($key, $direction) = $tmp;
				$ds->orderBy($key, $direction);
			}

			if ($limit !== NULL OR $offset)
			{
				$ds->applyLimit($limit, $offset);
			}

			$this->dataSource = $ds;
		}
		return $this->dataSource;
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
		$this->dataSource = NULL;
		parent::release();
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	final public function getTotalCount()
	{
		throw new NotImplementedException();
	}

	/** @deprecated */
	final public function fetchSingle()
	{
		throw new DeprecatedException(array(__CLASS__, 'fetchSingle()', __CLASS__ . '->getDataSource()->fetchSingle()'));
	}

	/** @deprecated */
	final public function select($col, $as = NULL)
	{
		throw new DeprecatedException(array(__CLASS__, 'select()', __CLASS__ . '->getDataSource()->select()'));
	}
}
