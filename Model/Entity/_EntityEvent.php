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
		$this->checkEvent = true;
	}

	/** Vytazena z mapperu */
	protected function onLoad(IRepository $repository)
	{
		$this->checkEvent = true;
	}

	/** Pred persistovanim (insert nebo update) */
	protected function onBeforePersist(IRepository $repository)
	{
		$this->checkEvent = true;
	}
	/** Po persistovani (insert nebo update) */
	protected function onAfterPersist(IRepository $repository)
	{
		$this->checkEvent = true;
	}

	/** Behem persistovani, vsechny subentity nemusi byt jeste persistovany */
	protected function onPersist(IRepository $repository, $id)
	{
		$this->checkEvent = true;
	}

	/** Pred vymazanim */
	protected function onBeforeDelete(IRepository $repository)
	{
		$this->checkEvent = true;
	}
	/** Po vymazani */
	protected function onAfterDelete(IRepository $repository)
	{
		$this->checkEvent = true;
	}

	/** Persistovane zmeny (update) */
	protected function onBeforeUpdate(IRepository $repository)
	{
		$this->checkEvent = true;
	}
	/** Persistovane zmeny (update) */
	protected function onAfterUpdate(IRepository $repository)
	{
		$this->checkEvent = true;
	}

	/** Persistovane zmeny (insert) */
	protected function onBeforeInsert(IRepository $repository)
	{
		$this->checkEvent = true;
	}
	/** Persistovane zmeny (insert) */
	protected function onAfterInsert(IRepository $repository)
	{
		$this->checkEvent = true;
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

		if ($entity->checkEvent !== true)
		{
			$class = get_class($entity);
			throw new InvalidStateException("Method $class::$method() or its descendant doesn't call parent::$method().");
		}
	}

}
