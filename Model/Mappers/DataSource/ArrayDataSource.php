<?php

require_once dirname(__FILE__) . '/IModelDataSource.php';


class ArrayDataSource extends Object implements IModelDataSource
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
		throw new NotImplementedException();
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
	public function orderBy($row, $sorting = 'ASC')
	{
		if (is_array($row)) {
			$this->sorting = $row;
		} else {
			$this->sorting[$row] = $sorting;
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
	private function _sort($v1, $v2)
	{
		$k = key($this->_sort);
		$s = current($this->_sort);
		dd($k, $s);
		if ($s == 'ASC')
		{
			return strcasecmp($v1[$k], $v2[$k]);
		}
		else
		{
			return strcasecmp($v2[$k], $v1[$k]);
		}
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

			foreach (array_reverse($this->sorting) as $row => $sorting)
			{
				$this->_sort = array($row => $sorting);
				uasort($source, array($this,'_sort'));
			}
			$this->_sort = NULL;

			if ($this->offset !== NULL OR $this->limit !== NULL)
			{
				$source = array_slice($source, (int) $this->offset, $this->limit, true);
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
		throw new NotImplementedException();
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
}


