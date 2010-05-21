<?php


abstract class AbstractModel extends Object
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

	public function & __get($name)
	{
		$r = $this->getRepository($name);
		return $r;
	}

	/**
	 * @return AppModel
	 */
	public static function get()
	{
		static $model;
		if (!isset($model))
		{
			$model = new Model;
			if (!($model instanceof self))
			{
				throw new Error;
			}
		}
		return $model;
	}

}

class StdObject extends stdClass implements ArrayAccess
{
	public function __construct(array $arr)
	{
		foreach ($arr as $k => $v) $this->$k = $v;
	}

	public function toArray()
	{
		return (array) $this;
	}

	public function offsetExists($key)
	{
		return isset($this->{$key});
	}
	public function offsetGet($key)
	{
		return $this->{$key};
	}
	public function offsetSet($key, $value)
	{
		$this->{$key} = $value;
	}
	public function offsetUnset($key)
	{
		unset($this->{$key});
	}
}

class ModelDataSource extends DibiDataSourceX implements IModelDataSource
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
		$entity = $this->repository->getEntityName();
		if (is_array($row))
		{
			return parent::orderBy($conventional->format($row, $entity), $sorting);
		}
		else
		{
			$row = $conventional->format(array($row => $sorting), $entity);
			return parent::orderBy(key($row), $sorting);
		}
	}

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
			//return Entity::create($this->repository->getEntityName($a), $this->repository->getConventional()->unformat((array) $a));
		}
		else if (is_array($a))
		{
			$a = array_map(array($this, __FUNCTION__), $a);
		}
		return $a;
	}


}

class EntityIterator extends IteratorIterator implements Countable
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
		return $this->repository->createEntity($row === false ? NULL : $row);
		//return Entity::create($this->repository->getEntityName($row), $this->repository->getConventional()->unformat((array) $row));
	}

	public function count()
	{
		return $this->getInnerIterator()->count();
	}

}

