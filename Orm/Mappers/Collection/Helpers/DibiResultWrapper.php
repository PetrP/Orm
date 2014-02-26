<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Countable;
use IteratorAggregate;
use DibiResult;

/**
 * DibiResult wrapper.
 * Hydrates entity and caches result.
 * @see BaseDibiCollection::getResult()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection\Helpers
 */
class DibiResultWrapper extends Object implements Countable, IteratorAggregate
{

	/** @var IRepository */
	private $repository;

	/** @var DibiResult */
	private $dibiResult;

	/** @var IDibiResultDriver */
	private $dibiResultDriver;

	/** @var int */
	private $currentPosition = 0;

	/** @var DibiResult */
	private $cachedResult = array();

	/**
	 * @param IRepository
	 * @param DibiResult
	 */
	public function __construct(IRepository $repository, DibiResult $dibiResult)
	{
		$this->repository = $repository;
		$this->dibiResult = $dibiResult;
		$this->dibiResultDriver = $dibiResult->getResultDriver();
	}

	/**
	 * Fetches all entries to array.
	 * @return array of IEntity
	 */
	public function toArray()
	{
		$array = array();
		$position = 0;
		while ($entity = $this->get($position))
		{
			$array[] = $entity;
			$position++;
		}
		return $array;
	}

	/**
	 * Returns the number of entries.
	 * @return int
	 */
	public function count()
	{
		return $this->dibiResultDriver->getRowCount();
	}

	/** @return DibiResultWrapperIterator */
	public function getIterator()
	{
		return new DibiResultWrapperIterator($this);
	}

	/**
	 * Fetches all records like $key => $value pairs.
	 * @param string associative key
	 * @param string value
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL)
	{
		$this->currentPosition = -1;
		return $this->dibiResult->fetchPairs($key, $value);
	}

	/**
	 * Returns entry from specific position.
	 * @param int
	 * @return IEntity|NULL
	 */
	public function get($position)
	{
		if (isset($this->cachedResult[$position]))
		{
			$entity = $this->cachedResult[$position];
			if ($entity === false) return NULL;
			return $entity;
		}
		if ($this->currentPosition !== $position)
		{
			if ($this->resultSeek($position))
			{
				$this->currentPosition = $position;
			}
			else
			{
				$this->currentPosition = -1;
				$this->cachedResult[$position] = false;
				return NULL;
			}
		}
		$row = $this->resultFetch();
		if ($row === false)
		{
			$this->currentPosition = -1;
			$this->cachedResult[$position] = false;
			return NULL;
		}
		else
		{
			$this->currentPosition++;
			$entity = $this->repository->hydrateEntity($row);
			$this->cachedResult[$position] = $entity;
			return $entity;
		}
	}

	/**
	 * Moves cursor position without fetching row.
	 * @param int
	 * @return bool
	 */
	protected function resultSeek($position)
	{
		return $this->dibiResult->seek($position);
	}

	/**
	 * Fetches the row at current position,
	 * and moves the internal cursor to the next position.
	 * @return array|false array on success, FALSE if no next record
	 */
	protected function resultFetch()
	{
		return $this->dibiResult->fetch(true);
	}

}
