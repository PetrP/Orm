<?php

namespace Orm;

use Nette\Object;
use Dibi;
use DibiConnection;
use Nette\NotImplementedException;
use Nette\InvalidArgumentException;

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
	private $result;

	/** @var int */
	private $count;

	/** @var array @todo private */
	protected $where = array();

	/** @var array @todo private */
	protected $findBy = array();

	/** @var array */
	private $sorting = array();

	/** @var array */
	private $sourceSorting = array();

	/** @var int */
	private $limit;

	/** @var int */
	private $sourceLimit;

	/** @var int */
	private $offset;

	/** @var int */
	private $sourceOffset;

	/** @var array @see self::join() */
	private $join = array();

	/** @var IConventional @see self::getConnventionalKey() */
	private $conventional;

	/**
	 * @param string
	 * @param DibiConnection
	 * @param IRepository
	 */
	final public function __construct($tableName, DibiConnection $connection, IRepository $repository)
	{
		$this->tableName = $tableName;
		$this->repository = $repository;
		$this->connection = $connection;
		$this->conventional = $repository->getMapper()->getConventional();
	}

	/**
	 * Selects columns to order by.
	 * @param string|array column name or array of column names
	 * @param string sorting direction Dibi::ASC or Dibi::DESC
	 * @return DibiCollection $this
	 */
	final public function orderBy($key, $direction = Dibi::ASC)
	{
		if (is_array($key))
		{
			$this->sorting = array();
			foreach ($key as $name => $direction)
			{
				$this->orderBy((string) $name, $direction);
			}
		}
		else
		{
			$direction = strtoupper($direction);
			if ($direction !== Dibi::ASC AND $direction !== Dibi::DESC)
			{
				$direction = func_get_arg(1);
				throw new InvalidArgumentException(__CLASS__ . "::orderBy() Direction excepted Dibi::ASC or Dibi::DESC, '$direction' given");
			}

			if ($join = $this->repository->getMapper()->getJoinInfo($key))
			{
				$this->join($key);
				$key = $join->key;
			}
			else
			{
				$key = 'e.' . $this->getConnventionalKey($key);
			}

			$this->sorting[] = array($key, $direction);
		}
		$this->result = NULL;
		return $this;
	}

	/**
	 * Limits number of rows.
	 * @param int
	 * @param int
	 * @return DibiCollection $this
	 */
	final public function applyLimit($limit, $offset = NULL)
	{
		$this->limit = $limit;
		$this->offset = $offset;
		$this->result = $this->count = NULL;
		return $this;
	}

	/**
	 * Fetches the single row.
	 * @return IEntity|NULL
	 * @todo posouva cursor
	 */
	final public function fetch()
	{
		$row = $this->getResult()->fetch();
		if ($row === false) return NULL;
		return $this->repository->createEntity($row);
	}

	/**
	 * Fetches all records.
	 * @return array of IEntity
	 */
	final public function fetchAll()
	{
		return array_map(array($this->repository, 'createEntity'), $this->getResult()->fetchAll());
	}

	/**
	 * Fetches all records and returns associative tree.
	 * @param string associative descriptor
	 * @return array
	 */
	final public function fetchAssoc($assoc)
	{
		return FetchAssoc::apply($this->fetchAll(), $assoc);
	}

	/**
	 * Fetches all records like $key => $value pairs.
	 * @param string associative key
	 * @param string value
	 * @return array
	 */
	final public function fetchPairs($key = NULL, $value = NULL)
	{
		return $this->getResult()->fetchPairs(
			$key !== NULL ? $this->getConnventionalKey($key) : NULL,
			$value !== NULL ? $this->getConnventionalKey($value) : NULL
		);
	}

	/** @return EntityIterator */
	final public function getIterator()
	{
		return new EntityIterator($this->repository, $this->getResult()->getIterator());
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 * @todo optimalozovat
	 */
	final public function count()
	{
		return count($this->getResult());
	}

	/**
	 * Vraci kolekci entit dle kriterii.
	 * @param array
	 * @return DibiCollection
	 */
	final public function findBy(array $where)
	{
		$all = $this->toCollection();
		$all->findBy[] = $where;
		return $all;
	}

	/**
	 * Vraci jednu entitu dle kriterii.
	 * @param array
	 * @return IEntity|NULL
	 */
	final public function getBy(array $where)
	{
		return $this->findBy($where)->applyLimit(1)->fetch();
	}

	/**
	 * @param mixed
	 * @return DibiCollection $this
	 * @todo nepridava e.
	 */
	final public function where($cond)
	{
		$this->where[] = is_array($cond) ? $cond : func_get_args();
		$this->result = $this->count = NULL;
		return $this;
	}

	/**
	 * Pripoji asociaci.
	 * @see DibiMapper::getJoinInfo()
	 * @param string
	 * @return DibiCollection $this
	 * @todo public?
	 */
	final public function join($key)
	{
		$lastAlias = 'e';
		foreach ($this->repository->getMapper()->getJoinInfo($key)->joins as $join)
		{
			if (!isset($this->join[$join['alias']]))
			{
				$this->join[$join['alias']] = array(
					"LEFT JOIN %n as %n ON %n = %n",
					$join['table'], $join['alias'], $join['alias'] . '.' . $join['yConventionalKey'], $lastAlias . '.' . $join['xConventionalKey']
				);
				if ($join['findBy'])
				{
					$findBy = $join['findBy'];
					$where = array();
					FindByHelper::dibiProcess(
						$this,
						$this->repository->getMapper(),
						$this->conventional,
						$where,
						$findBy,
						$join['alias']
					);
					$this->join[$join['alias']][] = 'AND %and';
					$this->join[$join['alias']][] = $where;
				}
			}
			$lastAlias = $join['alias'];
		}
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

	/** @return DibiCollection */
	final public function toCollection()
	{
		list($sorting, $limit, $offset) = $this->process();
		$collection = clone $this;
		$collection->sourceSorting = $sorting;
		$collection->sourceLimit = $limit;
		$collection->sourceOffset = $offset;
		$collection->sorting = array();
		$collection->limit = NULL;
		$collection->offset = NULL;
		return $collection;
	}

	/**
	 * Vola automaticky findBy* a getBy*
	 * <pre>
	 * 	$collection->findByAuthor(3);
	 * 	// stejne jako
	 * 	$collection->findBy(array('author' => 3));
	 *
	 * 	$collection->findByAuthorAndCategory(3, 'foo');
	 * 	// stejne jako
	 * 	$collection->findBy(array('author' => 3, 'category' => 'foo'));
	 * </pre>
	 * @see self::findBy();
	 * @see self::getBy();
	 * @param string
	 * @param array
	 * @throws MemberAccessException
	 * @return DibiCollection|IEntity|NULL
	 */
	final public function __call($name, $args)
	{
		if (!method_exists($this, $name) AND FindByHelper::parse($name, $args))
		{
			return $this->$name($args);
		}
		return parent::__call($name, $args);
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

	/**
	 * Returns sql query
	 * @return string
	 */
	final public function __toString()
	{
		list($sorting, $limit, $offset) = $this->process();
		$orderBy = array();
		end($sorting); $end = key($sorting);
		foreach ($sorting as $i => $tmp)
		{
			list($key, $direction) = $tmp;
			$orderBy[] = '%by' . ($end === $i ? '' : ', ');
			$orderBy[] = array($key => $direction);
		}
		FindByHelper::dibiProcess(
			$this,
			$this->repository->getMapper(),
			$this->conventional,
			$this->where,
			$this->findBy
		);

		$join = array();
		foreach ($this->join as $tmp) $join = array_merge($join, $tmp);

		return $this->connectionTranslate('
			SELECT [e.*]
			FROM %n', $this->tableName, ' as e
			%ex', $join,'
			%ex', $this->where ? array('WHERE %and', $this->where) : NULL, '
			' . ($join ? 'GROUP BY [e.id]' : '') . '
			%ex', $orderBy ? array('ORDER BY %sql', $orderBy) : NULL, '
			%ofs %lmt', $offset, $limit
		);
	}

	/**
	 * Use DibiConnection::translate() or DibiConnection::sql()
	 * @return string sql
	 */
	final private function connectionTranslate($args)
	{
		static $translate;
		if ($translate === NULL)
		{
			$translate = method_exists($this->connection, 'translate') ? 'translate' : 'sql';
		}
		$args = func_get_args();
		return $this->connection->$translate($args);
	}

	/**
	 * Merge source and this sorting, $limit and $offset
	 * @see self::$sorting
	 * @see self::$sourceSorting
	 * @see self::$limit
	 * @see self::$sourceLimit
	 * @see self::$offset
	 * @see self::$sourceOffset
	 * @return array $sorting, $limit, $offset
	 * @todo private
	 */
	final protected function process()
	{
		$limit = $this->limit;
		$offset = $this->sourceOffset + $this->offset;
		if ($this->sourceLimit !== NULL)
		{
			$limit = max(0, $this->sourceLimit - $this->offset);
			if ($this->limit !== NULL)
			{
				$limit = min($this->limit, $limit);
			}
			$offset = min($offset, $this->sourceOffset + $this->sourceLimit);
		}

		$sorting = array_merge($this->sorting, $this->sourceSorting);

		return array($sorting, $limit, $offset);
	}

	/**
	 * @param string
	 * @return string
	 */
	final protected function getConnventionalKey($key)
	{
		$tmp = $this->conventional->formatEntityToStorage(array($key => NULL));
		return key($tmp);
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	final public function getTotalCount()
	{
		throw new NotImplementedException();
	}

}
