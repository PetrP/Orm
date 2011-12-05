<?php

use Orm\Object;
use Orm\Repository;
use Orm\IRepository;
use Orm\IEntity;

class RepositoryContainer_checkRepositoryClass_BadRepository extends Object {}
abstract class RepositoryContainer_checkRepositoryClass_Bad2Repository extends Repository {}
class RepositoryContainer_checkRepositoryClass_Bad3Repository extends Object implements IRepository
{
	protected function __construct()
	{
	}

	public function getById($id) {}
	public function attach(IEntity $entity) {}
	public function persist(IEntity $entity, $all = true) {}
	public function remove($entity) {}
	public function flush() {}
	public function clean() {}
	public function getMapper() {}
	public function getModel() {}
	public function getEntityClassName(array $data = NULL) {}
	public function lazyLoad(IEntity $entity, $param) {}
	public function isAttachableEntity(IEntity $entity) {}
	public function hydrateEntity($data) {}
	public function getEvents() {}
	public function getIdentityMap() {}
}
