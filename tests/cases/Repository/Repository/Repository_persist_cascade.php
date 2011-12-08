<?php

use Orm\Repository;
use Orm\ArrayManyToManyMapper;
use Orm\IEntity;
use Orm\IRepository;

/**
 * @property Repository_persist_cascade2_Entity $m1 {m:1 Repository_persist_cascade2_}
 */
class Repository_persist_cascade1_Entity extends TestEntity
{
}

class Repository_persist_cascade1_Repository extends Repository
{
	protected $entityClassName = 'Repository_persist_cascade1_Entity';
}

class Repository_persist_cascade1_Mapper extends TestsMapper
{
	public $dump;
	public function persist(IEntity $entity)
	{
		$result = parent::persist($entity);
		$this->dump[] = $entity->m1->id;
		return $result;
	}
}

/**
 * @property Orm\OneToMany $1m {1:m Repository_persist_cascade1_ m1}
 */
class Repository_persist_cascade2_Entity extends TestEntity
{
}

class Repository_persist_cascade2_Repository extends Repository
{
	protected $entityClassName = 'Repository_persist_cascade2_Entity';
}

class Repository_persist_cascade2_Mapper extends TestsMapper
{
}

/**
 * @property Orm\ManyToMany $mm {m:m Repository_persist_cascade4_ mm mapped}
 */
class Repository_persist_cascade3_Entity extends TestEntity
{
}

class Repository_persist_cascade3_Repository extends Repository
{
	protected $entityClassName = 'Repository_persist_cascade3_Entity';
}

class Repository_persist_cascade3_Mapper extends TestsMapper
{
	public $dump;
	public $dumpMany;
	public function persist(IEntity $entity)
	{
		$result = parent::persist($entity);
		$this->dump[] = $entity->mm->getInjectedValue();
		return $result;
	}

	public function createManyToManyMapper($firstParam, IRepository $repository, $secondParam)
	{
		$many = new Repository_persist_cascade_ArrayManyToManyMapper;
		$this->dumpMany[] = & $many->dump;
		return $many;
	}
}

class Repository_persist_cascade_ArrayManyToManyMapper extends ArrayManyToManyMapper
{
	public $dump;

	public function add(IEntity $parent, array $ids, $injectedValue)
	{
		$this->dump[] = array('add', $parent->id, $ids);
		return parent::add($parent, $ids, $injectedValue);
	}

	public function remove(IEntity $parent, array $ids, $injectedValue)
	{
		$this->dump[] = array('remove', $parent->id, $ids);
		return parent::remove($parent, $ids, $injectedValue);
	}
}

/**
 * @property Orm\ManyToMany $mm {m:m Repository_persist_cascade3_ mm}
 */
class Repository_persist_cascade4_Entity extends TestEntity
{
}

class Repository_persist_cascade4_Repository extends Repository
{
	protected $entityClassName = 'Repository_persist_cascade4_Entity';
}

class Repository_persist_cascade4_Mapper extends TestsMapper
{
	public $dump;
	public function persist(IEntity $entity)
	{
		$result = parent::persist($entity);
		$this->dump[] = $entity->mm->getInjectedValue();
		return $result;
	}
}
