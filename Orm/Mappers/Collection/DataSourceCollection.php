<?php

namespace Orm;

use DibiDataSourceX;
use DibiRow;
use DateTime;
use Nette\DeprecatedException;
use DibiConnection;

require_once dirname(__FILE__) . '/IEntityCollection.php';
require_once dirname(__FILE__) . '/Helpers/EntityIterator.php';
require_once dirname(__FILE__) . '/Helpers/FindByHelper.php';

class DataSourceCollection extends DibiDataSourceX implements IEntityCollection, DibiModelDataSource
{
	/** @var Repository */
	private $repository;

	/**
	 * Selects columns to order by.
	 * @param  string|array  column name or array of column names
	 * @param  string  		 sorting direction
	 * @return DataSourceCollection  provides a fluent interface
	 */
	final public function orderBy($row, $sorting = 'ASC')
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

	final public function __construct($sql, DibiConnection $connection, IRepository $repository)
	{
		$this->repository = $repository;
		parent::__construct($sql, $connection);
	}

	final public function getResult()
	{
		return parent::getResult();
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
		return $this->createEntityRecursive($row === false ? NULL : $row);
	}



	/**
	 * Like fetch(), but returns only first field.
	 * @return mixed  value on success, FALSE if no next record
	 */
	final public function fetchSingle()
	{
		return $this->getResult()->fetchSingle();
	}



	/**
	 * Fetches all records from table.
	 * @return array
	 */
	final public function fetchAll()
	{
		return $this->createEntityRecursive($this->getResult()->fetchAll());
	}



	/**
	 * Fetches all records from table and returns associative tree.
	 * @param  string  associative descriptor
	 * @return array
	 */
	final public function fetchAssoc($assoc)
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
	final public function fetchPairs($key = NULL, $value = NULL)
	{
		/** @var SqlConventional */
		$conventional = $this->repository->getMapper()->getConventional();

		if ($key !== NULL)
		{
			$key = $conventional->formatEntityToStorage(array($key => NULL));
			$key = key($key);
		}
		if ($value !== NULL)
		{
			$value = $conventional->formatEntityToStorage(array($value => NULL));
			$value = key($value);
		}

		return $this->createEntityRecursive($this->getResult()->fetchPairs($key, $value));
	}


	private function createEntityRecursive($a)
	{
		if ($a instanceof DibiRow)
		{
			return $this->repository->createEntity($a);
		}
		else if (is_array($a))
		{
			$a = array_map(array($this, __FUNCTION__), $a);
		}
		return $a;
	}

	/** @return ArrayCollection */
	final public function toArrayCollection()
	{
		return new ArrayCollection($this->fetchAll());
	}

	/** @return DataSourceCollection */
	public function toCollection()
	{
		$class = get_class($this);
		return new $class($this->__toString(), $this->connection, $this->repository);
	}

	final public function findBy(array $where)
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
						create_function('$v', 'return $v instanceof Orm\IEntity ? (isset($v->id) ? $v->id : NULL) : $v;'),
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
		return $all->toCollection();
	}

	final public function getBy(array $where)
	{
		return $this->findBy($where)->applyLimit(1)->fetch();
	}

	final public function __call($name, $args)
	{
		if (!method_exists($this, $name) AND FindByHelper::parse($name, $args))
		{
			return $this->$name($args);
		}
		return parent::__call($name, $args);
	}

	final protected function getConnventional()
	{
		return $this->repository->getMapper()->getConventional();
	}

	final protected function getConnventionalKey($key)
	{
		$tmp = $this->repository->getMapper()->getConventional()->formatEntityToStorage(array($key => NULL));
		return key($tmp);
	}

	// todo final count totalCount a toString a dalsi

	/** @deprecated */
	final public function toArrayDataSource(){throw new DeprecatedException('Use Orm\DataSourceCollection::toArrayCollection() instead');}
	/** @deprecated */
	final public function toDataSource(){throw new DeprecatedException('Use Orm\DataSourceCollection::toCollection() instead');}
}
