<?php

namespace Orm;

use Nette\NotSupportedException;
use Nette\NotImplementedException;
use Nette\DeprecatedException;
use DibiDataSourceX;
use DibiDataSource;
use DibiConnection;

require_once dirname(__FILE__) . '/IEntityCollection.php';
require_once dirname(__FILE__) . '/BaseDibiCollection.php';

class DataSourceCollection extends BaseDibiCollection implements IEntityCollection
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
				$dsClass = 'DibiDataSource';
				if (class_exists('DibiDataSourceX'))
				{
					$dsClass = 'DibiDataSourceX';
				}
			}

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
		throw new DeprecatedException('DataSourceCollection::fetchSingle() is deprecated; use DataSourceCollection->getDataSource()->fetchSingle() instead');
	}

	/** @deprecated */
	final public function select($col, $as = NULL)
	{
		throw new DeprecatedException('DataSourceCollection::select() is deprecated; use DataSourceCollection->getDataSource()->select() instead');
	}
}
