<?php

use Orm\Entity;
use Orm\Repository;
use Orm\IRepository;
use Orm\IEntity;

class Repository_remove_Entity extends Entity
{

	static $beforeRemove;
	static $afterRemove;

	protected function onBeforeRemove(IRepository $repository)
	{
		call_user_func(self::$beforeRemove, (object) array(
			'entity' => $this,
			'repository' => $repository,
			'type' => 'beforeRemove',
		));
		parent::onBeforeRemove($repository);
	}

	protected function onAfterRemove(IRepository $repository)
	{
		call_user_func(self::$afterRemove, (object) array(
			'entity' => $this,
			'repository' => $repository,
			'type' => 'afterRemove',
		));
		parent::onAfterRemove($repository);
	}

}

class Repository_remove_Repository extends Repository
{
	protected $entityClassName = 'Repository_remove_Entity';
}

class Repository_remove_Mapper extends TestsMapper
{
	public $count = 0;

	public $returnNull = false;

	public function remove(IEntity $entity)
	{
		$this->count++;
		if ($this->returnNull) return NULL;
		return parent::remove($entity);
	}
}
