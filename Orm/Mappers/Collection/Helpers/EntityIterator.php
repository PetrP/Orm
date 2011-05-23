<?php

namespace Orm;

use IteratorIterator;
use Countable;
use DibiResultIterator;

class EntityIterator extends IteratorIterator implements Countable
{
	private $repository;

	public function __construct(IRepository $repository, DibiResultIterator $iterator)
	{
		$this->repository = $repository;
		parent::__construct($iterator);
	}

	public function current()
	{
		return $this->repository->createEntity(parent::current());
	}

	public function count()
	{
		return $this->getInnerIterator()->count();
	}

}
