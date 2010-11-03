<?php

require_once dirname(__FILE__) . '/IModelDataSource.php';

require_once dirname(__FILE__) . '/IEntityCollection.php';

require_once dirname(__FILE__) . '/EntityIterator.php';

require_once dirname(__FILE__) . '/StdObject.php';


class DibiModelDataSource extends DibiDataSourceX implements IModelDataSource, IEntityCollection
{
	/** @var Repository */
	private $repository;

	/**
	 * Selects columns to order by.
	 * @param  string|array  column name or array of column names
	 * @param  string  		 sorting direction
	 * @return DibiDataSource  provides a fluent interface
	 */
	public function orderBy($row, $sorting = 'ASC')
	{
		$conventional = $this->repository->getMapper()->getConventional();
		if (is_array($row))
		{
			return parent::orderBy($conventional->formatEntityToStorage($row), $sorting);
		}
		else
		{
			$row = $conventional->formatEntityToStorage(array($row => $sorting));
			return parent::orderBy(key($row), $sorting);
		}
	}

	public function __construct($sql, DibiConnection $connection, IRepository $repository)
	{
		$this->repository = $repository;
		parent::__construct($sql, $connection);
	}

	public function getResult()
	{
		$result = parent::getResult();
		return $result->setRowClass('StdObject');
	}

	/**
	 * @return DibiResultIterator
	 */
	public function getIterator()
	{
		return new EntityIterator($this->repository, $this->getResult()->getIterator());
	}



	/**
	 * Generates, executes SQL query and fetches the single row.
	 * @return DibiRow|FALSE  array on success, FALSE if no next record
	 */
	public function fetch()
	{
		$row = $this->getResult()->fetch();
		return $this->createEntityRecursive($row === false ? NULL : $row);
	}



	/**
	 * Like fetch(), but returns only first field.
	 * @return mixed  value on success, FALSE if no next record
	 */
	public function fetchSingle()
	{
		return $this->getResult()->fetchSingle();
	}



	/**
	 * Fetches all records from table.
	 * @return array
	 */
	public function fetchAll()
	{
		return $this->createEntityRecursive($this->getResult()->fetchAll());
	}



	/**
	 * Fetches all records from table and returns associative tree.
	 * @param  string  associative descriptor
	 * @return array
	 */
	public function fetchAssoc($assoc)
	{
		// todo conventional
		return $this->createEntityRecursive($this->getResult()->fetchAssoc($assoc));
	}



	/**
	 * Fetches all records from table like $key => $value pairs.
	 * @param  string  associative key
	 * @param  string  value
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL)
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

		return $this->createEntityRecursive($this->getResult()->fetchPairs($key, $value));
	}


	private function createEntityRecursive($a)
	{
		if ($a instanceof StdObject)
		{
			return $this->repository->createEntity($a);
		}
		else if (is_array($a))
		{
			$a = array_map(array($this, __FUNCTION__), $a);
		}
		return $a;
	}

	/** @return ArrayDataSource */
	public function toArrayDataSource()
	{
		return new ArrayDataSource($this->fetchAll());
	}

	/** @return DibiModelDataSource */
	public function toDataSource()
	{
		return new DibiModelDataSource($this->__toString(), $this->connection, $this->repository);
	}

	protected function findBy(array $where)
	{
		$all = $this->toDataSource();
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
				$all->where('%n IN %in', $key, $value);
			}
			else if ($value === NULL)
			{
				$all->where('%n IS NULL', $key);
			}
			else if ($value instanceof DateTime)
			{
				$all->where('%n = %t', $key, $value);
			}
			else
			{
				$all->where('%n = %s', $key, $value);
			}
		}
		return $all->toDataSource();
	}

	protected function getBy(array $where)
	{
		return $this->findBy($where)->applyLimit(1)->fetch();
	}

	public function __call($name, $args)
	{
		try {
			return parent::__call($name, $args);
		} catch (MemberAccessException $e) {

			$mode = $by = NULL;
			if (substr($name, 0, 6) === 'findBy')
			{
				$mode = 'find';
				$by = substr($name, 6);
			}
			else if (substr($name, 0, 5) === 'getBy')
			{
				$mode = 'get';
				$by = substr($name, 5);
			}

			if ($mode AND $by)
			{
				$where = array();
				// todo prvni na male pismeno udelat rychleji
				foreach (array_map(create_function('$v', 'if ($v{0} != "_") $v{0} = $v{0} | "\x20"; return $v;'),explode('And', $by)) as $n => $key) // lcfirst
				{
					if (!array_key_exists($n, $args)) throw new InvalidArgumentException("There is no value for '$key'.");
					$where[$key] = $args[$n];
				}
				return $mode === 'get' ? $this->getBy($where) : $this->findBy($where);
			}

			throw $e;
		}
	}

}
class ModelDataSource extends DibiModelDataSource {}
