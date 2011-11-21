<?php

use Orm\Object;
use Orm\IRepository;
use Orm\IEntity;
use Orm\Events;

class RepositoryContainer_clean1Repository extends Object implements IRepository
{
	public $count = array();
	public function clean()
	{
		$this->count[] = 'repo';
	}

	public function getById($id) {}
	public function attach(IEntity $entity) {}
	public function persist(IEntity $entity) {}
	public function remove($entity) {}
	public function flush() {}
	public function getMapper()
	{
		return new RepositoryContainer_clean1Mapper($this);
	}
	public function getModel() {}
	public function getEntityClassName(array $data = NULL) {}
	public function lazyLoad(IEntity $entity, $param) {}
	public function isAttachableEntity(IEntity $entity) {}
	public function hydrateEntity($data) {}
	public function getEvents()
	{
		return new Events($this);
	}
	public function persistAll() {}
}

class RepositoryContainer_clean1Mapper extends Object
{
	private $repo;
	public function __construct(RepositoryContainer_clean1Repository $repo)
	{
		$this->repo = $repo;
	}

	public function rollback()
	{
		$this->repo->count[] = 'mapper';
	}
}

class RepositoryContainer_clean2Repository extends RepositoryContainer_clean1Repository
{

}
