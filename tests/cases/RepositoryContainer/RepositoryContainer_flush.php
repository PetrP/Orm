<?php

use Nette\Object;
use Orm\IRepository;
use Orm\IEntity;

class RepositoryContainer_flush1Repository extends Object implements IRepository
{
	public $count = array();
	public function flush($onlyThis = false)
	{
		$this->count[] = $onlyThis;
	}

	public function getById($id) {}
	public function attach(IEntity $entity) {}
	public function persist(IEntity $entity) {}
	public function remove($entity) {}
	public function clean($onlyThis = false) {}
	public function getRepositoryName() {}
	public function getMapper() {}
	public function getModel() {}
	public function getEntityClassName(array $data = NULL) {}
	public function lazyLoad(IEntity $entity, $param) {}
	public function isEntity(IEntity $entity) {}
	public function createEntity($data) {}
}

class RepositoryContainer_flush2Repository extends RepositoryContainer_flush1Repository
{

}
