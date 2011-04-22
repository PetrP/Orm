<?php

class EntityEvent_Entity extends Entity
{
	public $event;
	public $eventParam;

	protected function onCreate()
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		parent::onCreate();
	}

	protected function onBeforePersist(IRepository $repository)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		parent::onBeforePersist($repository);
	}

	protected function onLoad(IRepository $repository, array $data)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		parent::onLoad($repository, $data);
	}

}

class EntityEvent2_Entity extends Entity
{
	protected function onBeforePersist(IRepository $repository)
	{
	}

	protected function onAfterPersist(IRepository $repository)
	{
		parent::onAfterRemove($repository);
	}

	protected function onUserDefined()
	{
		throw new InvalidStateException;
	}

}

class EntityEventRepository extends Repository
{
	protected $entityClassName = 'EntityEvent_Entity';
}

class EntityEventMapper extends ArrayMapper
{

}
