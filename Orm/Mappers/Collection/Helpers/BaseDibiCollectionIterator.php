<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Iterator;
use Countable;

/**
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection\Helpers
 */
class BaseDibiCollectionIterator implements Iterator, Countable
{

	private $repository;
	private $iterator;
	private $start;
	private $end;

	/**
	 * @param IRepository
	 * @param callback return Traversable
	 * @param callback
	 */
	public function __construct(IRepository $repository, $start, $end)
	{
		$this->repository = $repository;
		$this->start = $start;
		$this->end = $end;
	}

	public function rewind()
	{
		$this->stop();
		$this->start();
		$this->iterator->rewind();
	}

	public function next()
	{
		$this->iterator->next();
	}

	public function valid()
	{
		$valid = $this->iterator->valid();
		if (!$valid)
		{
			$this->stop();
		}
		return $valid;
	}

	public function current()
	{
		return $this->iterator->current();
	}

	public function key()
	{
		return $this->iterator->key();
	}

	public function count()
	{
		$this->start();
		$this->iterator->count();
	}

	public function __clone()
	{
		throw new NotSupportedException;
	}

	private function start()
	{
		if (!$this->iterator)
		{
			$this->iterator = new HydrateEntityIterator($this->repository, call_user_func($this->start));
		}
	}

	private function stop()
	{
		if ($this->iterator)
		{
			call_user_func($this->end);
			$this->iterator = NULL;
		}
	}


}
