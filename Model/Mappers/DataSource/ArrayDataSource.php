<?php

require_once dirname(__FILE__) . '/IModelDataSource.php';

require_once dirname(__FILE__) . '/IEntityCollection.php';

require_once dirname(__FILE__) . '/FetchAssoc.php';

class ArrayDataSource extends Object implements IModelDataSource, IEntityCollection
{
	protected $source;

	/** @var array */
	protected $result;

	/** @var int */
	protected $count;

	/** @var int */
	protected $totalCount;

	/** @var array */
	protected $cols = array();

	/** @var array */
	protected $sorting = array();

	/** @var array */
	protected $conds = array();

	/** @var int */
	protected $offset;

	/** @var int */
	protected $limit;



	public function __construct(array $source)
	{
		$this->source = $source;
	}



	/**
	 * Selects columns to query.
	 * @param  string|array  column name or array of column names
	 * @param  string  		 column alias
	 * @return DibiDataSource  provides a fluent interface
	 */
	public function select($col, $as = NULL)
	{
		throw new NotImplementedException();
		if (is_array($col)) {
			$this->cols = $col;
		} else {
			$this->cols[$col] = $as;
		}
		$this->result = NULL;
		return $this;
	}



	/**
	 * Adds conditions to query.
	 * @param  mixed  conditions
	 * @return DibiDataSource  provides a fluent interface
	 */
	public function where($cond)
	{
		if (is_array($cond)) {
			// TODO: not consistent with select and orderBy
			$this->conds[] = $cond;
		} else {
			$this->conds[] = func_get_args();
		}
		$this->result = $this->count = NULL;
		return $this;
	}



	/**
	 * Selects columns to order by.
	 * @param  string|array  column name or array of column names
	 * @param  string  		 sorting direction
	 * @return DibiDataSource  provides a fluent interface
	 */
	public function orderBy($row, $direction = Dibi::ASC)
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
	 * @return DibiDataSource  provides a fluent interface
	 */
	public function applyLimit($limit, $offset = NULL)
	{
		$this->limit = $limit;
		$this->offset = $offset;
		$this->result = $this->count = NULL;
		return $this;
	}


	/********************* executing ****************d*g**/

	private $_sort;
	private function _sort($aRow, $bRow)
	{
		foreach ($this->_sort as $tmp)
		{
			$key = $tmp[0];
			$direction = $tmp[1];
			if (!$aRow->hasParam($key) OR !$bRow->hasParam($key))
			{
				throw new InvalidArgumentException("'$key' is not key");
			}

			$a = $aRow->{$key};
			$b = $bRow->{$key};

			if (is_scalar($a) AND is_scalar($b))
			{
				$r = strnatcasecmp($a, $b);
			}
			else if ($a instanceof DateTime AND $b instanceof DateTime)
			{
				$r = $a < $b ? -1 : 1;
			}
			else if ($b === NULL)
			{
				$r = 1;
			}
			else if ($a === NULL)
			{
				$r = -1;
			}
			else
			{
				throw new InvalidArgumentException("'$key' is not sortable key");
			}

			if ($r !== 0)
			{
				break;
			}
		}

		if ($direction === Dibi::DESC) return -$r;
		return $r;
	}
	/**
	 * Returns (and queries) DibiResult.
	 * @return DibiResult
	 */
	public function getResult()
	{
		if ($this->result === NULL)
		{
			$source = $this->source;

			if ($this->conds)
			{
				if (count($this->conds) === 1 AND count($this->conds[0]) === 2 AND preg_match('#^\s*\[id\]\s*\=\s*\%i\s*$#',$this->conds[0][0]) AND is_numeric($this->conds[0][1]))
				{
					$copySource = $source;
					$source = array();
					foreach ($copySource as $row)
					{
						if ($row['id'] == $this->conds[0][1])
						{
							$source[] = $row;
						}
					}
				}
				else
				{
					throw new NotImplementedException();
				}
			}

			if ($this->sorting)
			{
				$this->_sort = $this->sorting;
				$this->_sort[] = array('id', Dibi::ASC);
				uasort($source, array($this, '_sort'));
				$this->_sort = NULL;
			}

			if ($this->offset !== NULL OR $this->limit !== NULL)
			{
				$source = array_slice($source, (int) $this->offset, $this->limit);
			}

			$this->result = $source;
		}
		return $this->result;
	}



	/**
	 * @return DibiResultIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->getResult());
	}



	/**
	 * Generates, executes SQL query and fetches the single row.
	 * @return DibiRow|FALSE  array on success, FALSE if no next record
	 */
	public function fetch()
	{
		$row = current($this->getResult());
		return $row === false ? NULL : $row;
	}



	/**
	 * Like fetch(), but returns only first field.
	 * @return mixed  value on success, FALSE if no next record
	 */
	public function fetchSingle()
	{
		throw new NotImplementedException();
		return $this->getResult()->fetchSingle();
	}



	/**
	 * Fetches all records from table.
	 * @return array
	 */
	public function fetchAll()
	{
		return $this->getResult();
	}



	/**
	 * Fetches all records from table and returns associative tree.
	 * @param  string  associative descriptor
	 * @return array
	 */
	public function fetchAssoc($assoc)
	{
		return FetchAssoc::apply($this->fetchAll(), $assoc);
	}



	/**
	 * Fetches all records from table like $key => $value pairs.
	 * @param  string  associative key
	 * @param  string  value
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL)
	{
		$row = $this->fetch();
		if (!$row) return array();  // empty result set

		$data = array();

		if ($value === NULL) {
			if ($key !== NULL) {
				throw new InvalidArgumentException("Either none or both columns must be specified.");
			}

			// autodetect
			$tmp = array_keys($row->toArray());
			$key = $tmp[0];
			if (count($row) < 2) { // indexed-array
				foreach ($this->getResult() as $row)
				{
					$data[] = $row[$key];
				}
				return $data;
			}

			$value = $tmp[1];

		} else {
			if (!$row->hasParam($value)) {
				throw new InvalidArgumentException("Unknown value column '$value'.");
			}

			if ($key === NULL) { // indexed-array
				foreach ($this->getResult() as $row)
				{
					$data[] = $row[$value];
				}
				return $data;
			}

			if (!$row->hasParam($key)) {
				throw new InvalidArgumentException("Unknown key column '$key'.");
			}
		}

		foreach ($this->getResult() as $row)
		{
			$data[ $row[$key] ] = $row[$value];
		}

		return $data;
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	public function count()
	{
		return count($this->getResult());
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	public function getTotalCount()
	{
		return count($this->data);
	}

	/** @return ArrayDataSource */
	public function toArrayDataSource()
	{
		return new ArrayDataSource($this->getResult());
	}

	/** @return ArrayDataSource */
	public function toDataSource()
	{
		return $this->toArrayDataSource();
	}

	protected function findBy(array $where)
	{
		foreach ($where as $key => $value)
		{
			if (is_array($value))
			{
				$value = array_unique(
					array_map(
						create_function('$v', 'return $v instanceof IEntity ? $v->id : $v;'),
						$value
					)
				);
				$where[$key] = $value;
			}
			else if ($value instanceof IEntityCollection)
			{
				$where[$key] = $value->fetchPairs(NULL, 'id');
			}
			else if ($value instanceof IEntity)
			{
				$value = isset($value->id) ? $value->id : NULL;
				$where[$key] = $value;
			}
		}

		$all = $this->getResult();
		$result = array();
		foreach ($all as $entity)
		{
			$equal = false;
			foreach ($where as $key => $value)
			{
				$eValue = $entity[$key];
				$eValue = $eValue instanceof IEntity ? (isset($eValue->id) ? $eValue->id : NULL) : $eValue;

				if ($eValue == $value OR (is_array($value) AND in_array($eValue, $value)))
				{
					$equal = true;
				}
				else
				{
					$equal = false;
					break;
				}
			}
			if ($equal)
			{
				$result[] = $entity;
			}
		}

		return new ArrayDataSource($result);
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
