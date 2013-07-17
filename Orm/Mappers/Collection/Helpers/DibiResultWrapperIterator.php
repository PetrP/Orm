<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Countable;
use Iterator;

/**
 * Iterate DibiResultWrapper.
 * @see DibiResultWrapper
 * @see BaseDibiCollection::getIterator()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection\Helpers
 */
class DibiResultWrapperIterator implements Iterator, Countable
{

	/** @var DibiResultWrapper */
	private $result;

	/** @var IEntity|NULL */
	private $row;

	/** @var int */
	private $pointer;

	/**
	 * @param DibiResultWrapper
	 */
	public function __construct(DibiResultWrapper $result)
	{
		$this->result = $result;
	}

	/**
	 * Rewinds the iterator to the first element.
	 * @return void
	 */
	public function rewind()
	{
		$this->pointer = 0;
		$this->row = $this->result->get(0);
	}

	/**
	 * Returns the key of the current element.
	 * @return mixed
	 */
	public function key()
	{
		return $this->pointer;
	}

	/**
	 * Returns the current element.
	 * @return mixed
	 */
	public function current()
	{
		return $this->row;
	}

	/**
	 * Moves forward to next element.
	 * @return void
	 */
	public function next()
	{
		$this->pointer++;
		$this->row = $this->result->get($this->pointer);
	}

	/**
	 * Checks if there is a current element after calls to rewind() or next().
	 * @return bool
	 */
	public function valid()
	{
		return $this->row !== NULL;
	}

	/**
	 * Required by the Countable interface.
	 * @return int
	 */
	public function count()
	{
		return $this->result->count();
	}

}
