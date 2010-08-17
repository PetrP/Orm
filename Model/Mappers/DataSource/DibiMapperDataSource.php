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
		// todo conventional $key a value;
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


}
class ModelDataSource extends DibiModelDataSource {}
