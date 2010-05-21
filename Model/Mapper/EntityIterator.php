<?php

class EntityIterator extends IteratorIterator implements Countable
{
	private $repository;

	public function __construct(Repository $repository, DibiResultIterator $iterator)
	{
		$this->repository = $repository;
		parent::__construct($iterator);
	}

	public function current()
	{
		$row = parent::current();
		return $this->repository->createEntity($row === false ? NULL : $row);
		//return Entity::create($this->repository->getEntityName($row), $this->repository->getConventional()->unformat((array) $row));
	}

	public function count()
	{
		return $this->getInnerIterator()->count();
	}

}
