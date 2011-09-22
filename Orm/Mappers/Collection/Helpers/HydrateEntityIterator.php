<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use IteratorIterator;
use Countable;
use Traversable;

/**
 * Hydrate iterator for IEntityCollection.
 * @see IRepository::hydrateEntity()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection\Helpers
 */
class HydrateEntityIterator extends IteratorIterator implements Countable
{
	/** @var IRepository */
	private $repository;

	/**
	 * @param IRepository
	 * @param Traversable
	 */
	public function __construct(IRepository $repository, Traversable $iterator)
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
		$i = $this->getInnerIterator();
		if ($i instanceof Countable)
		{
			return $i->count();
		}
		return iterator_count($i);
	}

}
