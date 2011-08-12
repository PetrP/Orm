<?php

use Orm\Repository;
use Orm\ArrayMapper;
use Orm\IRepository;
use Orm\IEntity;

/**
 * @property Repository_persist_recursion1_Entity $m1 {m:1 Repository_persist_recursion1_}
 * @property Orm\OneToMany $1m {1:m Repository_persist_recursion1_ m1}
 */
class Repository_persist_recursion1_Entity extends TestEntity
{
}

class Repository_persist_recursion1_Repository extends Repository
{
	protected $entityClassName = 'Repository_persist_recursion1_Entity';
}

class Repository_persist_recursion1_Mapper extends Repository_persist_cascade1_Mapper
{
}

/**
 * @property Orm\ManyToMany $mma {m:m Repository_persist_recursion2_ mmb mapped}
 * @property Orm\ManyToMany $mmb {m:m Repository_persist_recursion2_ mma}
 */
class Repository_persist_recursion2_Entity extends TestEntity
{
	protected function onAfterPersist(IRepository $repository)
	{
		parent::onAfterPersist($repository);
		$this->markAsChanged();
	}
}

class Repository_persist_recursion2_Repository extends Repository
{
	protected $entityClassName = 'Repository_persist_recursion2_Entity';
}

class Repository_persist_recursion2_Mapper extends Repository_persist_cascade3_Mapper
{
	public function persist(IEntity $entity)
	{
		$result = ArrayMapper::persist($entity);
		$this->dump[] = array($entity->mma->getInjectedValue(), $entity->mmb->getInjectedValue());
		return $result;
	}
}
