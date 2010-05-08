<?php


class Model extends Object
{
	private static $repositories = array();
	
	public static function getRepository($name)
	{
		$name = strtolower($name);
		if (!isset(self::$repositories[$name]))
		{
			$class = $name . 'Repository';
			$class[0] = strtoupper($class[0]);
			
			$r = new $class($name);
			if (!($r instanceof Repository))
			{
				throw new InvalidStateException();
			}
			self::$repositories[$name] = $r;
		}
		return self::$repositories[$name];
	}
	
}

class StdObject extends stdClass
{
	public function __construct(array $arr)
	{
		foreach ($arr as $k => $v) $this->$k = $v;
	}
}

class ModelDataSource extends DibiDataSourceX
{
	private $repository;
	
	public function __construct($sql, DibiConnection $connection, Repository $repository)
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
		return $this->repository->createEntity($this->getResult()->fetch());
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
		return array_map(array($this->repository, 'createEntity'), $this->getResult()->fetchAll());
	}



	/**
	 * Fetches all records from table and returns associative tree.
	 * @param  string  associative descriptor
	 * @return array
	 */
	public function fetchAssoc($assoc)
	{
		throw new NotImplementedException(); // todo
		return $this->getResult()->fetchAssoc($assoc);
	}



	/**
	 * Fetches all records from table like $key => $value pairs.
	 * @param  string  associative key
	 * @param  string  value
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL)
	{
		return array_map(array($this->repository, 'createEntity'), $this->getResult()->fetchPairs($key, $value));
	}
	
}

class EntityIterator extends IteratorIterator
{
	private $repository;
	
	public function __construct(Repository $repository, DibiResultIterator $iterator)
	{
		$this->repository = $repository;
		parent::__construct($iterator);
	}
	
	public function current()
	{
		$row = parent::current();
		return $this->repository->createEntity($row);
	}
	
}

