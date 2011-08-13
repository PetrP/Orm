<?php

use Orm\Mapper;
use Orm\IEntity;
use Orm\IRepository;
use Orm\IConventional;
use Orm\NotImplementedException;

class Mapper_getConventional_Mapper extends Mapper
{
	public $c;
	protected function createConventional()
	{
		if ($this->c) return $this->c;
		return parent::createConventional();
	}

	public function findAll()
	{
		throw new NotImplementedException;
	}

	public function getById($id)
	{
		throw new NotImplementedException;
	}

	public function persist(IEntity $entity)
	{
		throw new NotImplementedException;
	}

	public function remove(IEntity $entity)
	{
		throw new NotImplementedException;
	}

	public function flush()
	{
		throw new NotImplementedException;
	}

	public function rollback()
	{
		throw new NotImplementedException;
	}

	public function createManyToManyMapper($param, IRepository $targetRepository, $targetParam)
	{
		throw new NotImplementedException;
	}

	protected function createCollectionClass()
	{
		throw new NotImplementedException;
	}

}

class Mapper_getConventional_Conventional implements IConventional
{
	public function formatEntityToStorage($data)
	{
		throw new NotImplementedException;
	}

	public function formatStorageToEntity($data)
	{
		throw new NotImplementedException;
	}
}
