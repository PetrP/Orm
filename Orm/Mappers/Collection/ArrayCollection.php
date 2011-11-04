<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use DateTime;
use ArrayIterator;

/**
 * Collection of entities which are already loaded in php memory.
 *
 * <code>
 * $collection = new ArrayCollection(array(
 * 	new FooEntity,
 * 	new FooEntity,
 * 	$repository->getById(123),
 * 	$repository->getById(124),
 * ));
 * </code>
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection
 */
class ArrayCollection extends Object implements IEntityCollection
{

	/** @var array */
	private $source;

	/** @var array */
	private $result;

	/** @var array */
	private $sorting = array();

	/** @var int */
	private $offset;

	/** @var int */
	private $limit;

	/** @var array temp from sorting @see self::_sort() */
	private $_sort;

	/** @param array */
	final public function __construct(array $source)
	{
		$tmp = array();
		foreach ($source as $entity) $tmp[spl_object_hash($entity)] = $entity;
		$this->source = array_values($tmp);
	}

	/**
	 * Selects columns to order by.
	 * @param string|array column name or array of column names
	 * @param string sorting direction self::ASC or self::DESC
	 * @return ArrayCollection $this
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

			$this->sorting[] = array($key, $direction);
		}
		$this->result = NULL;
		return $this;
	}

	/**
	 * Limits number of rows.
	 * @param int
	 * @param int
	 * @return ArrayCollection $this
	 */
	final public function applyLimit($limit, $offset = NULL)
	{
		$this->limit = $limit;
		$this->offset = $offset;
		$this->result = NULL;
		return $this;
	}

	/**
	 * Fetches the first row.
	 * @return IEntity|NULL
	 */
	final public function fetch()
	{
		$result = $this->getResult();
		$row = current($result);
		return $row === false ? NULL : $row;
	}

	/**
	 * Fetches all records.
	 * @return array of IEntity
	 */
	final public function fetchAll()
	{
		return $this->getResult();
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
		$row = $this->fetch();
		if (!$row) return array();

		if ($value === NULL)
		{
			throw new InvalidArgumentException("Value or both columns must be specified.");
		}
		else if (!$row->hasParam($value))
		{
			throw new InvalidArgumentException("Unknown value column '$value'.");
		}
		else if ($key !== NULL AND !$row->hasParam($key))
		{
			throw new InvalidArgumentException("Unknown key column '$key'.");
		}

		$data = array();
		foreach ($this->getResult() as $k => $row)
		{
			if ($key !== NULL)
			{
				$k = $row[$key];
			}
			$data[$k] = $row[$value];
		}
		return $data;
	}

	/** @return ArrayIterator */
	final public function getIterator()
	{
		return new ArrayIterator($this->getResult());
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	final public function count()
	{
		return count($this->getResult());
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 * @todo deprecated?
	 */
	final public function getTotalCount()
	{
		return count($this->source);
	}

	/**
	 * Vraci kolekci entit dle kriterii.
	 * @param array
	 * @return ArrayCollection
	 */
	final public function findBy(array $where)
	{
		foreach ($where as $key => $value)
		{
			if ($value instanceof IEntityCollection)
			{
				$where[$key] = $value->fetchPairs(NULL, 'id');
			}
		}

		$all = $this->getResult();
		$result = array();
		foreach ($all as $entity)
		{
			foreach ($where as $key => $value)
			{
				$eValue = $entity[$key];
				if ($eValue instanceof IEntity)
				{
					if ($value instanceof IEntity AND $eValue === $value)
					{
						continue;
					}
					else if (is_scalar($value) AND isset($eValue->id) AND $eValue->id == $value)
					{
						continue;
					}
					else if (is_array($value))
					{
						if (in_array($eValue, $value, true))
						{
							continue;
						}
						else if (isset($eValue->id) AND in_array($eValue->id, $value))
						{
							continue;
						}
					}
					continue 2;
				}
				else if ($value instanceof IEntity)
				{
					continue 2;
				}
				else if ($eValue == $value OR (is_array($value) AND in_array($eValue, $value)))
				{
					continue;
				}
				continue 2;
			}
			$result[] = $entity;
		}

		$class = get_class($this);
		return new $class($result);
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

	/** @return ArrayCollection */
	final public function toArrayCollection()
	{
		return new ArrayCollection($this->getResult());
	}

	/** @return ArrayCollection */
	final public function toCollection()
	{
		$class = get_class($this);
		return new $class($this->getResult());
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
	 * @return ArrayCollection|IEntity|NULL
	 */
	final public function __call($name, $args)
	{
		if (!method_exists($this, $name) AND FindByHelper::parse($name, $args))
		{
			return $this->$name($args);
		}
		return parent::__call($name, $args);
	}

	/** @return array */
	final public function getResult()
	{
		if ($this->result === NULL)
		{
			$source = $this->source;
			if ($this->sorting)
			{
				$this->_sort = $this->sorting;
				$this->_sort[] = array('id', self::ASC);
				usort($source, array($this, '_sort'));
				$this->_sort = NULL;
			}

			if ($this->offset !== NULL OR $this->limit !== NULL)
			{
				if ($this->limit === NULL)
				{
					// php <= 5.2.3 bug #41686
					$source = array_slice($source, (int) $this->offset);
				}
				else
				{
					$source = array_slice($source, (int) $this->offset, $this->limit);
				}
			}

			$this->result = $source;
		}
		return $this->result;
	}

	/**
	 * usort comparison function
	 * @see self::getResult()
	 * @see self::$_sort
	 * @param IEntity
	 * @param IEntity
	 * @return int -1 or 1
	 */
	final private function _sort(IEntity $aRow, IEntity $bRow)
	{
		foreach ($this->_sort as $tmp)
		{
			$key = $tmp[0];
			$direction = $tmp[1];
			if (strpos($key, '->') !== false)
			{
				$a = $aRow;
				$b = $bRow;
				foreach (explode('->', $key) as $k)
				{
					if (!($a instanceof IEntity)) $a = NULL;
					else if (!$a->hasParam($k))
					{
						throw new InvalidArgumentException("'$k' is not key in '{$key}'");
					}
					else $a = $a->{$k};

					if (!($b instanceof IEntity)) $b = NULL;
					else if (!$b->hasParam($k))
					{
						throw new InvalidArgumentException("'$k' is not key in '{$key}'");
					}
					else $b = $b->{$k};
				}
			}
			else
			{
				if (!$aRow->hasParam($key) OR !$bRow->hasParam($key))
				{
					if (!isset($aRow->{$key}) OR !isset($bRow->{$key}))
					{
						throw new InvalidArgumentException("'$key' is not key");
					}
				}

				$a = $aRow->{$key};
				$b = $bRow->{$key};
			}

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
				$tmp = 'unknown';
				foreach (array($a, $b) as $ab)
				{
					if (!is_scalar($ab) AND !($ab instanceof DateTime) AND $ab !== NULL)
					{
						$tmp = is_object($ab) ? get_class($ab) : gettype($ab);
						break;
					}
				}
				throw new InvalidArgumentException(get_class($aRow) . "::\$$key contains non-sortable value, $tmp");
			}

			if ($r !== 0)
			{
				break;
			}
		}

		if ($direction === self::DESC) return -$r;
		return $r;
	}

}
