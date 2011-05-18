<?php

namespace Orm;

use Nette\InvalidStateException;

/**
 * Uchovava stav o repository kde je entita persistovana.
 * @see Entity
 * @property-read RepositoryContainer $model
 * @todo zvazit jestli to neni spatny navrh
 */
class _EntityGeneratingRepository extends _EntityEvent
{
	/** @var IRepository|NULL null kdyz jeste nebylo ulozeno */
	private $repository;

	/** @var RepositoryContainer|NULL cache */
	private $model;

	/**
	 * Vytazena z mapperu
	 * @param IRepository
	 * @param array
	 */
	protected function onLoad(IRepository $repository, array $data)
	{
		parent::onLoad($repository, $data);
		$this->repository = $repository;
	}

	/**
	 * Pripojeno na repository
	 * @param IRepository
	 */
	protected function onAttach(IRepository $repository)
	{
		parent::onAttach($repository);
		$this->repository = $repository;
	}

	/**
	 * Po vymazani
	 * @param IRepository
	 */
	protected function onAfterRemove(IRepository $repository)
	{
		parent::onAfterRemove($repository);
		$this->repository = NULL;
		$this->model = NULL;
	}

	/**
	 * Repository ktery se o tuto entitu stara.
	 * Existuje jen kdyz entita byla persistovana.
	 * @param bool
	 * @throws InvalidStateException
	 * @return IRepository |NULL
	 */
	final public function getGeneratingRepository($need = true) // todo generating je blbost, lepsi nazev by bylo neco jako getOwningReppository nebo jen getRepository
	{
		if (!$this->repository AND $need)
		{
			$tmp = get_class($this) . (isset($this->id) ? '#' . $this->id : NULL);
			throw new InvalidStateException("{$tmp} is not attached to repository.");
		}
		return $this->repository;
	}

	/**
	 * @param bool
	 * @return RepositoryContainer
	 */
	final public function getModel($need = true)
	{
		if (!isset($this->model))
		{
			if ($need === NULL AND !$this->getGeneratingRepository(false)) // bc
			{
				return RepositoryContainer::get(); // todo di
			}
			if ($r = $this->getGeneratingRepository($need))
			{
				$this->model = $r->getModel();
			}
		}
		return $this->model;
	}

}
