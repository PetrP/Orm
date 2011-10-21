<?php

use Orm\Object;
use Orm\IRepository;
use Orm\IEntity;
use Orm\Events;

class RepositoryContainer_flush1Repository extends Object implements IRepository
{
	public $count = array();
	public function flush()
	{
		$this->count[] = 'repo';
	}

	public function getById($id) {}
	public function attach(IEntity $entity) {}
	public function persist(IEntity $entity) {}
	public function remove($entity) {}
	public function clean() {}
	public function getMapper()
	{
		return new RepositoryContainer_flush1Mapper($this);
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
}

class RepositoryContainer_flush1Mapper extends Object
{
	private $repo;
	public function __construct(RepositoryContainer_flush1Repository $repo)
	{
		$this->repo = $repo;
	}

	public function flush()
	{
		$this->repo->count[] = 'mapper';
	}
}

class RepositoryContainer_flush2Repository extends RepositoryContainer_flush1Repository
{

}
