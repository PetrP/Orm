<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Events.
 * @see Entity
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\EntityFragments
 */
abstract class EventEntityFragment extends Object
{
	/**
	 * Pro kontrolu jestli bylo v podedene udalosti volano parent::on*.
	 * Obrahuje nazev posledni volane udalosti.
	 * @var string|NULL
	 */
	private $checkEvent;

	public function __construct()
	{
		$this->fireEvent('onCreate');
	}

	/** Vytvorena nova entita */
	protected function onCreate()
	{
		$this->checkEvent = 'onCreate';
	}

	/**
	 * Vytazena z mapperu
	 * @param IRepository
	 * @param array
	 */
	protected function onLoad(IRepository $repository, array $data)
	{
		$this->checkEvent = 'onLoad';
	}

	/**
	 * Pripojeno na repository
	 * @param IRepository
	 */
	protected function onAttach(IRepository $repository)
	{
		$this->checkEvent = 'onAttach';
	}

	/**
	 * Pred persistovanim (insert nebo update)
	 * @param IRepository
	 */
	protected function onBeforePersist(IRepository $repository)
	{
		$this->checkEvent = 'onBeforePersist';
	}

	/**
	 * Po persistovani (insert nebo update)
	 * @param IRepository
	 */
	protected function onAfterPersist(IRepository $repository)
	{
		$this->checkEvent = 'onAfterPersist';
	}

	/**
	 * Behem persistovani, vsechny subentity nemusi byt jeste persistovany
	 * @param IRepository
	 * @param scalar
	 */
	protected function onPersist(IRepository $repository, $id)
	{
		$this->checkEvent = 'onPersist';
	}

	/**
	 * Pred vymazanim
	 * @param IRepository
	 */
	protected function onBeforeRemove(IRepository $repository)
	{
		$this->checkEvent = 'onBeforeRemove';
	}

	/**
	 * Po vymazani
	 * @param IRepository
	 */
	protected function onAfterRemove(IRepository $repository)
	{
		$this->checkEvent = 'onAfterRemove';
	}

	/**
	 * Persistovane zmeny (update)
	 * @param IRepository
	 */
	protected function onBeforeUpdate(IRepository $repository)
	{
		$this->checkEvent = 'onBeforeUpdate';
	}

	/**
	 * Persistovane zmeny (update)
	 * @param IRepository
	 */
	protected function onAfterUpdate(IRepository $repository)
	{
		$this->checkEvent = 'onAfterUpdate';
	}

	/**
	 * Persistovane zmeny (insert)
	 * @param IRepository
	 */
	protected function onBeforeInsert(IRepository $repository)
	{
		$this->checkEvent = 'onBeforeInsert';
	}

	/**
	 * Persistovane zmeny (insert)
	 * @param IRepository
	 */
	protected function onAfterInsert(IRepository $repository)
	{
		$this->checkEvent = 'onAfterInsert';
	}

	/**
	 * Do not call directly!
	 * Vola urcitou udalost.
	 * @internal
	 * @param string nazev udalosti
	 * @param IRepository
	 * @param array|scalar $data (onLoad) or $id (onPersist)
	 */
	final public function fireEvent($method, IRepository $repository = NULL, $more = NULL)
	{
		if (!method_exists(__CLASS__, $method))
		{
			$class = get_class($this);
			throw new InvalidEntityException("Call to undefined event $class::$method().");
		}

		$this->checkEvent = NULL;
		if ($more === NULL)
		{
			$this->{$method}($repository);
		}
		else
		{
			$this->{$method}($repository, $more);
		}

		if ($this->checkEvent !== $method)
		{
			$class = get_class($this);
			throw new InvalidEntityException("Method $class::$method() or its descendant doesn't call parent::$method().");
		}
		$this->checkEvent = NULL;
	}

	/** @deprecated */
	final static public function ___event(IEntity $entity, $event, IRepository $repository = NULL, $more = NULL)
	{
		throw new DeprecatedException(array('Orm\Entity', '___event()', 'Orm\Entity', 'fireEvent()'));
		//$entity->fireEvent('on' . ucfirst($event), $repository, $more);
	}

}
