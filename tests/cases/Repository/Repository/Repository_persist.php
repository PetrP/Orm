<?php

use Orm\Repository;
use Orm\IRepository;
use Orm\IEntity;

class Repository_persist_Entity extends TestEntity
{
	protected function onAfterPersist(IRepository $repository)
	{
		parent::onAfterPersist($repository);
		$this->string .= '_changedDuringPersist';
	}
}

class Repository_persist_Repository extends Repository
{
	protected $entityClassName = 'TestEntity';
}

class Repository_persist_Mapper extends TestsMapper
{
	public $count = 0;

	public $returnNull = false;

	public function persist(IEntity $entity)
	{
		$this->count++;
		if ($this->returnNull) return NULL;
		return parent::persist($entity);
	}
}

class Repository_persist2_Repository extends Repository
{
	protected $entityClassName = 'Repository_persist_Entity';
}

class Repository_persist2_Mapper extends Repository_persist_Mapper
{

}
