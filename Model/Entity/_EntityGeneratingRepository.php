<?php

class _EntityGeneratingRepository extends _EntityEvent
{
	private $repository;

	/** Vytazena z mapperu */
	protected function onLoad(IRepository $repository)
	{
		parent::onLoad($repository);
		$this->repository = $repository;
	}

	/** Behem persistovani, vsechny subentity nemusi byt jeste persistovany */
	protected function onPersist(IRepository $repository, $id)
	{
		parent::onPersist($repository, $id);
		$this->repository = $repository; // todo jen kdyz neni isset?
	}

	/** Po vymazani */
	protected function onAfterDelete(IRepository $repository)
	{
		parent::onAfterDelete($repository);
		$this->repository = NULL;
	}

	// todo zvazit
	final public function getGeneratingRepository($need = true) // todo generating je blbost, lepsi nazev by bylo neco jako getOwningReppository nebo jen getRepository
	{
		if ($this->repository) return $this->repository;
		else if (!$need) return NULL;
		else throw new InvalidStateException();
	}

	final public function getModel($need = true)
	{
		$need = false; // todo
		if ($this->getGeneratingRepository($need))
		{
			return $this->getGeneratingRepository()->getModel();
		}
		return Model::get(); // todo di
		return NULL;
	}

}
