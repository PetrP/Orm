<?php

use Orm\Entity;
use Orm\Repository;
use Orm\ArrayMapper;
use Orm\IRepository;

/**
 * @property $var
 */
class EventEntityFragment_Entity extends Entity
{
	public $event;
	public $eventParam;

	public $all = array();

	protected function onCreate()
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		parent::onCreate();
	}

	protected function onLoad(IRepository $repository, array $data)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		parent::onLoad($repository, $data);
	}

	protected function onBeforePersist(IRepository $repository)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		$this->all[] = array($this->event, $this->eventParam);
		parent::onBeforePersist($repository);
	}

	protected function onAfterPersist(IRepository $repository)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		$this->all[] = array($this->event, $this->eventParam);
		parent::onAfterPersist($repository);
	}
/*
	protected function onPersist(IRepository $repository, $id)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		$this->all[] = array($this->event, $this->eventParam);
		parent::onPersist($repository, $id);
	}
 */
	protected function onBeforeRemove(IRepository $repository)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		$this->all[] = array($this->event, $this->eventParam);
		parent::onBeforeRemove($repository);
	}

	protected function onAfterRemove(IRepository $repository)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		$this->all[] = array($this->event, $this->eventParam);
		parent::onAfterRemove($repository);
	}

	protected function onBeforeUpdate(IRepository $repository)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		$this->all[] = array($this->event, $this->eventParam);
		parent::onBeforeUpdate($repository);
	}

	protected function onAfterUpdate(IRepository $repository)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		$this->all[] = array($this->event, $this->eventParam);
		parent::onAfterUpdate($repository);
	}

	protected function onBeforeInsert(IRepository $repository)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		$this->all[] = array($this->event, $this->eventParam);
		parent::onBeforeInsert($repository);
	}

	protected function onAfterInsert(IRepository $repository)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		$this->all[] = array($this->event, $this->eventParam);
		parent::onAfterInsert($repository);
	}

	protected function onAttach(IRepository $repository)
	{
		$this->event = __FUNCTION__;
		$this->eventParam = func_get_args();
		$this->all[] = array($this->event, $this->eventParam);
		parent::onAttach($repository);
	}

}

class EventEntityFragment2_Entity extends Entity
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
		throw new Exception;
	}

}

class EventEntityFragmentRepository extends Repository
{
	protected $entityClassName = 'EventEntityFragment_Entity';
}

class EventEntityFragmentMapper extends ArrayMapper
{
	protected function loadData()
	{
		return array(
			1 => array(
				'id' => 1,
			),
		);
	}

	protected function saveData(array $data)
	{

	}
}
