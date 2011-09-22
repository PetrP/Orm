<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use DibiConnection;

/**
 * Common things for DibiCollection and DataSourceCollection.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection
 */
abstract class BaseDibiCollection extends Object implements IEntityCollection
{

	/** @var string */
	protected $tableAlias = '';

	/** @var IRepository @see self::getRepository() */
	private $repository;

	/** @var DibiConnection @see self::getConnection() */
	private $connection;

	/** @var IDatabaseConventional @see self::getConnventionalKey() @see self::getConnventional() */
	private $conventional;

	/** @var IMapper @see self::getMapper() */
	private $mapper;

	/** @var array cache */
	private $result;

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



	/**
	 * @param DibiConnection
	 * @param IRepository
	 */
	public function __construct(DibiConnection $connection, IRepository $repository)
	{
		$this->repository = $repository;
		$this->connection = $connection;
		$this->mapper = $repository->getMapper();
		$this->conventional = $this->mapper->getConventional();
	}

	/**
	 * Selects columns to order by.
	 * @param string|array column name or array of column names
	 * @param string sorting direction self::ASC or self::DESC
	 * @return DibiCollection $this
	 */
	final public function orderBy($key, $direction = self::ASC)
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
			if ($direction !== self::ASC AND $direction !== self::DESC)
			{
				$direction = func_get_arg(1);
				throw new InvalidArgumentException(array($this, 'orderBy() $direction', 'Orm\IEntityCollection::ASC or DESC', $direction));
			}

			if ($join = $this->mapper->getJoinInfo($key))
			{
				$this->join($key);
				$key = $join->key;
			}
			else
			{
				$key = $this->tableAlias . $this->getConnventionalKey($key);
			}

			$this->sorting[] = array($key, $direction);
		}
		$this->release();
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
		$this->release(true);
		return $this;
	}

	/**
	 * Fetches the first row.
	 * @return IEntity|NULL
	 */
	final public function fetch()
	{
		$result = $this->getResult();
		$result->seek(0);
		$row = $result->fetch();
		if ($row === false) return NULL;
		return $this->repository->hydrateEntity($row);
	}

	/**
	 * Fetches all records.
	 * @return array of IEntity
	 */
	final public function fetchAll()
	{
		return array_map(array($this->repository, 'hydrateEntity'), $this->getResult()->fetchAll());
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

	/** @return HydrateEntityIterator */
	final public function getIterator()
	{
		return new HydrateEntityIterator($this->repository, $this->getResult()->getIterator());
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
		$all->release(true);
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
		$this->release(true);
		return $this;
	}

	/**
	 * Pripoji asociaci.
	 * @see DibiMapper::getJoinInfo()
	 * @param string
	 * @return DibiCollection $this
	 * @todo public?
	 */
	abstract public function join($key);

	/** @return ArrayCollection */
	final public function toArrayCollection()
	{
		return new ArrayCollection($this->fetchAll());
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
	 * <code>
	 * 	$collection->findByAuthor(3);
	 * 	// stejne jako
	 * 	$collection->findBy(array('author' => 3));
	 *
	 * 	$collection->findByAuthorAndCategory(3, 'foo');
	 * 	// stejne jako
	 * 	$collection->findBy(array('author' => 3, 'category' => 'foo'));
	 * </code>
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
	abstract public function __toString();

	/** @return IRepository */
	final protected function getRepository()
	{
		return $this->repository;
	}

	/** @return DibiMapper */
	final protected function getMapper()
	{
		return $this->mapper;
	}

	/** @return DibiConnection */
	final protected function getConnection()
	{
		return $this->connection;
	}

	/** @return IDatabaseConventional */
	final protected function getConventional()
	{
		return $this->conventional;
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
	 * Discards the internal cache.
	 * @param bool
	 */
	protected function release($count = false)
	{
		$this->result = NULL;
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

}
