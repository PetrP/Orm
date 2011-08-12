<?php

use Orm\Mapper;
use Orm\IEntity;
use Orm\IRepository;

class Mapper_createConventional_Mapper extends Mapper
{
	public function __createConventional()
	{
		return $this->createConventional();
	}

	protected function createCollectionClass() {}
	public function findAll() {}
	public function persist(IEntity $entity) {}
	public function remove(IEntity $entity) {}
	public function flush() {}
	public function rollback() {}
	public function createManyToManyMapper($param, IRepository $targetRepository, $targetParam) {}
}
