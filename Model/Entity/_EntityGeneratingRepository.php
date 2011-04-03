<?php

/**
 * Uchovava stav o repository kde je entita persistovana.
 * @see Entity
 * @property-read Model $model
 * @todo zvazit jestli to neni spatny navrh
 */
class _EntityGeneratingRepository extends _EntityEvent
{
	/** @var IRepository|NULL null kdyz jeste nebylo ulozeno */
	private $repository;

	/** @var Model|NULL cache */
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
	 * Behem persistovani, vsechny subentity nemusi byt jeste persistovany
	 * @param IRepository
	 * @param int
	 */
	protected function onPersist(IRepository $repository, $id)
	{
		parent::onPersist($repository, $id);
		$this->repository = $repository; // todo jen kdyz neni isset?
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
		if ($this->repository) return $this->repository;
		else if (!$need) return NULL;
		else throw new InvalidStateException();
	}

	/**
	 * @param bool
	 * @return Model
	 */
	final public function getModel($need = true)
	{
		if (!isset($this->model))
		{
			$need = false; // todo
			if ($this->getGeneratingRepository($need))
			{
				$this->model = $this->getGeneratingRepository()->getModel();
			}
			$this->model = Model::get(); // todo di
		}
		return $this->model;
		return NULL;
	}

}
