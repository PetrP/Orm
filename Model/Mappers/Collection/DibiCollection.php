<?php

require_once dirname(__FILE__) . '/IEntityCollection.php';

require_once dirname(__FILE__) . '/Helpers/FetchAssoc.php';

require_once dirname(__FILE__) . '/Helpers/FindByHelper.php';

class DibiCollection extends Object implements IEntityCollection
{
	/** @var IRepository */
	protected $repository;

	/** @var DibiConnection */
	protected $connection;

	/** @var string */
	protected $tableName;

	/** @var array */
	protected $result;

	/** @var int */
	protected $count;

	/** @var int */
	protected $totalCount;

	/** @var array */
	protected $where = array();

	/** @var array */
	protected $sorting = array();

	/** @var array */
	protected $_sorting = array();

	/** @var int */
	protected $limit;

	/** @var int */
	protected $_limit;

	/** @var int */
	protected $_offset;

	/** @var int */
	protected $offset;

	final public function __construct($tableName, DibiConnection $connection, IRepository $repository)
	{
		$this->tableName = $tableName;
		$this->repository = $repository;
		$this->connection = $connection;
	}

	/**
	 * Selects columns to order by.
	 * @param  string|array  column name or array of column names
	 * @param  string  		 sorting direction
	 * @return DibiCollection  provides a fluent interface
	 */
	final public function orderBy($row, $direction = Dibi::ASC)
	{
		if (is_array($row))
		{
			$this->sorting = array();
			foreach ($row as $name => $direction)
			{
				$this->orderBy((string) $name, $direction);
			}
		}
		else
		{
			$direction = strtoupper($direction);
			if ($direction !== Dibi::ASC AND $direction !== Dibi::DESC)
			{
				if ($direction === false OR $direction === NULL) $direction = Dibi::ASC;
				else if ($direction === true) $direction = Dibi::DESC;
				else $direction = Dibi::ASC;
			}

			$this->sorting[] = array($row, $direction);
		}
		$this->result = NULL;
		return $this;
	}



	/**
	 * Limits number of rows.
	 * @param  int limit
	 * @param  int offset
	 * @return DibiCollection  provides a fluent interface
	 */
	final public function applyLimit($limit, $offset = NULL)
	{
		$this->limit = $limit;
		$this->offset = $offset;
		$this->result = $this->count = NULL;
		return $this;
	}

	/**
	 * Returns (and queries) DibiResult.
	 * @return DibiResult
	 */
	final public function getResult()
	{
		if ($this->result === NULL)
		{
			$this->result = $this->connection->nativeQuery($this->__toString());
		}
		return $this->result;
	}


	final protected function process()
	{
		$limit = $offset = NULL;

		if ($this->_offset !== NULL AND $this->offset !== NULL)
		{
			$offset = $this->offset+$this->_offset;
		}
		else if ($this->offset !== NULL)
		{
			$offset = $this->offset;
		}
		else if ($this->_offset !== NULL)
		{
			$offset = $this->_offset;
		}

		if ($this->_limit !== NULL AND $this->limit !== NULL)
		{
			$limit = min($this->limit, $this->_limit-$this->offset);
		}
		else if ($this->limit !== NULL)
		{
			$limit = $this->limit;
		}
		else if ($this->_limit !== NULL)
		{
			$limit = $this->_limit;
		}

		$sorting = array_merge($this->sorting, $this->_sorting);

		return array($sorting, $limit, $offset);
	}

	final public function __toString()
	{
		list($sorting, $limit, $offset) = $this->process();
		$orderBy = array();
		end($sorting); $end = key($sorting);
		foreach ($sorting as $i => $tmp)
		{
			list($row, $direction) = $tmp;
			$orderBy[] = '%by' . ($end === $i ? '' : ', ');
			$orderBy[] = array($row => $direction);
		}
		return $this->connectionTranslate('
			SELECT *
			FROM %n', $this->tableName, '
			%ex', $this->where ? array('WHERE %and', $this->where) : NULL, '
			%ex', $orderBy ? array('ORDER BY %sql', $orderBy) : NULL, '
			%ofs %lmt', $offset, $limit
		);
	}

	/**
	 * Use DibiConnection::translate() or DibiConnection::sql()
	 */
	protected function connectionTranslate( $args)
	{
		$args = func_get_args();
		$connection = $this->connection;
		return call_user_func_array(array($connection, method_exists($connection, 'translate') ? 'translate' : 'sql'), $args);
	}

	/**
	 * @return DibiResultIterator
	 */
	final public function getIterator()
	{
		return new EntityIterator($this->repository, $this->getResult()->getIterator());
	}

	/**
	 * Generates, executes SQL query and fetches the single row.
	 * @return DibiRow|FALSE  array on success, FALSE if no next record
	 */
	final public function fetch()
	{
		$row = $this->getResult()->fetch();
		if ($row === false) return NULL;
		return $this->repository->createEntity($row);
	}

	/**
	 * Fetches all records from table.
	 * @return array
	 */
	final public function fetchAll()
	{
		return array_map(array($this->repository, 'createEntity'), $this->getResult()->fetchAll());
	}

	/**
	 * Fetches all records from table and returns associative tree.
	 * @param  string  associative descriptor
	 * @return array
	 */
	final public function fetchAssoc($assoc)
	{
		return FetchAssoc::apply($this->fetchAll(), $assoc);
	}

	/**
	 * Fetches all records from table like $key => $value pairs.
	 * @param  string  associative key
	 * @param  string  value
	 * @return array
	 */
	final public function fetchPairs($key = NULL, $value = NULL)
	{
		/** @var SqlConventional */
		$conventional = $this->repository->getMapper()->getConventional();

		if ($key !== NULL)
		{
			$key = key($conventional->formatEntityToStorage(array($key => NULL)));
		}
		if ($value !== NULL)
		{
			$value = key($conventional->formatEntityToStorage(array($value => NULL)));
		}

		return $this->getResult()->fetchPairs($key, $value);
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	final public function count()
	{
		// todo
		return count($this->getResult());
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	final public function getTotalCount()
	{
		throw new NotImplementedException();
	}

	final protected function findBy(array $where)
	{
		$all = $this->toCollection();
		/** @var SqlConventional */
		$conventional = $this->repository->getMapper()->getConventional();
		$where = $conventional->formatEntityToStorage($where);
		foreach ($where as $key => $value)
		{
			if ($value instanceof IEntityCollection)
			{
				$value = $value->fetchPairs(NULL, 'id');
			}
			if ($value instanceof IEntity)
			{
				$value = isset($value->id) ? $value->id : NULL;
			}
			if (is_array($value))
			{
				$value = array_unique(
					array_map(
						create_function('$v', 'return $v instanceof IEntity ? (isset($v->id) ? $v->id : NULL) : $v;'),
						$value
					)
				);
				$all->where[] = array('%n IN %in', $key, $value);
			}
			else if ($value === NULL)
			{
				$all->where[] = array('%n IS NULL', $key);
			}
			else if ($value instanceof DateTime)
			{
				$all->where[] = array('%n = %t', $key, $value);
			}
			else
			{
				$all->where[] = array('%n = %s', $key, $value);
			}
		}
		return $all;
	}

	final protected function getBy(array $where)
	{
		return $this->findBy($where)->fetch(); // todo
		return $this->findBy($where)->applyLimit(1)->fetch();
	}

	final public function __call($name, $args)
	{
		try {
			return parent::__call($name, $args);
		} catch (MemberAccessException $e) {
			if (FindByHelper::parse($name, $args));
			{
				return $this->$name($args);
			}
			throw $e;
		}
	}

	final public function where($cond)
	{
		if (is_array($cond)) {
			// TODO: not consistent with select and orderBy
			$this->where[] = $cond;
		} else {
			$this->where[] = func_get_args();
		}
		$this->result = $this->count = NULL;
		return $this;
	}

	/** @return ArrayCollection */
	final public function toArrayCollection()
	{
		return new ArrayCollection($this->fetchAll());
	}

	/** @return DataSourceCollection */
	final public function toDataSourceCollection()
	{
		return new DataSourceCollection($this->__toString(), $this->connection, $this->repository);
	}

	/** @return ArrayCollection */
	final public function toCollection()
	{
		list($sorting, $limit, $offset) = $this->process();
		$collection = clone $this;
		$collection->_sorting = $sorting;
		$collection->_limit = $limit;
		$collection->_offset = $offset;
		$collection->sorting = array();
		$collection->limit = NULL;
		$collection->offset = NULL;
		return $collection;
	}

	/** @deprecated */
	final public function toArrayDataSource()
	{
		return $this->toArrayCollection();
	}

	/** @deprecated */
	public function toDataSource()
	{
		return $this->toDataSourceCollection();
	}
}
