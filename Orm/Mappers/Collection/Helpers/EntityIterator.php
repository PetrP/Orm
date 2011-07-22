<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use IteratorIterator;
use Countable;
use DibiResultIterator;

class EntityIterator extends IteratorIterator implements Countable
{
	/** @var IRepository */
	private $repository;

	/**
	 * @param IRepository
	 * @param DibiResultIterator
	 */
	public function __construct(IRepository $repository, DibiResultIterator $iterator)
	{
		$this->repository = $repository;
		parent::__construct($iterator);
	}

	/** @return IEntity */
	public function current()
	{
		return $this->repository->hydrateEntity(parent::current());
	}

	/** @return int */
	public function count()
	{
		return $this->getInnerIterator()->count();
	}

}
