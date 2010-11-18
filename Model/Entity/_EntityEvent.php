<?php

abstract class _EntityEvent extends _EntityMeta
{

	private $checkEvent;

	public function __construct()
	{
		$this->___event($this, 'create');
	}

	/** Vytvorena nova entita */
	protected function onCreate()
	{
		$this->checkEvent = 'onCreate';
	}

	/** Vytazena z mapperu */
	protected function onLoad(IRepository $repository)
	{
		$this->checkEvent = 'onLoad';
	}

	/** Pred persistovanim (insert nebo update) */
	protected function onBeforePersist(IRepository $repository)
	{
		$this->checkEvent = 'onBeforePersist';
	}
	/** Po persistovani (insert nebo update) */
	protected function onAfterPersist(IRepository $repository)
	{
		$this->checkEvent = 'onAfterPersist';
	}

	/** Behem persistovani, vsechny subentity nemusi byt jeste persistovany */
	protected function onPersist(IRepository $repository, $id)
	{
		$this->checkEvent = 'onPersist';
	}

	/** Pred vymazanim */
	protected function onBeforeDelete(IRepository $repository)
	{
		$this->checkEvent = 'onBeforeDelete';
	}
	/** Po vymazani */
	protected function onAfterDelete(IRepository $repository)
	{
		$this->checkEvent = 'onAfterDelete';
	}

	/** Persistovane zmeny (update) */
	protected function onBeforeUpdate(IRepository $repository)
	{
		$this->checkEvent = 'onBeforeUpdate';
	}
	/** Persistovane zmeny (update) */
	protected function onAfterUpdate(IRepository $repository)
	{
		$this->checkEvent = 'onAfterUpdate';
	}

	/** Persistovane zmeny (insert) */
	protected function onBeforeInsert(IRepository $repository)
	{
		$this->checkEvent = 'onBeforeInsert';
	}
	/** Persistovane zmeny (insert) */
	protected function onAfterInsert(IRepository $repository)
	{
		$this->checkEvent = 'onAfterInsert';
	}

	/**
	 * @internal
	 */
	final public static function ___event(IEntity $entity, $event, IRepository $repository = NULL, $id = NULL)
	{
		$method = 'on' . ucfirst($event);
		$entity->checkEvent = NULL;
		if ($id === NULL)
		{
			$entity->{$method}($repository);
		}
		else
		{
			$entity->{$method}($repository, $id);
		}

		if ($entity->checkEvent !== $method)
		{
			$class = get_class($entity);
			throw new InvalidStateException("Method $class::$method() or its descendant doesn't call parent::$method().");
		}
	}

}
