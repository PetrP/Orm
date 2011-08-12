<?php

use Nette\Object;
use Orm\IRepository;
use Orm\IEntity;

class RepositoryContainer_clean1Repository extends Object implements IRepository
{
	public $count = array();
	public function clean($onlyThis = false)
	{
		$this->count[] = $onlyThis;
	}

	public function getById($id) {}
	public function attach(IEntity $entity) {}
	public function persist(IEntity $entity) {}
	public function remove($entity) {}
	public function flush($onlyThis = false) {}
	public function getMapper() {}
	public function getModel() {}
	public function getEntityClassName(array $data = NULL) {}
	public function lazyLoad(IEntity $entity, $param) {}
	public function isAttachableEntity(IEntity $entity) {}
	public function hydrateEntity($data) {}
}

class RepositoryContainer_clean2Repository extends RepositoryContainer_clean1Repository
{

}
