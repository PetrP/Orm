<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Maintaining state about repository where is entity attached.
 * @see Entity
 * @property-read IRepositoryContainer $model
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\EntityFragments
 * @todo zvazit jestli to neni spatny navrh
 */
class AttachableEntityFragment extends EventEntityFragment
{
	/** @var IRepository|NULL null kdyz jeste nebylo ulozeno */
	private $repository;

	/** @var IRepositoryContainer|NULL|false */
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
	 * Pripojeno na model (nemusi se volat vzdy, ale jen kdyz jeste neni pripojeno na repository)
	 * @param IRepositoryContainer
	 */
	protected function onAttachModel(IRepositoryContainer $model)
	{
		parent::onAttachModel($model);
		if ($this->model === false)
		{
			throw new EntityWasRemovedException(array($this));
		}
		if ($this->model AND $model !== $this->model)
		{
			throw new EntityAlreadyAttachedException(array($this));
		}
		if ($this->repository AND $model !== $this->repository->getModel())
		{
			throw new EntityAlreadyAttachedException(array($this));
		}
		$this->model = $model;
	}

	/**
	 * Pripojeno na repository
	 * @param IRepository
	 */
	protected function onAttach(IRepository $repository)
	{
		parent::onAttach($repository);
		if ($this->model === false)
		{
			throw new EntityWasRemovedException(array($this));
		}
		if ($this->repository AND $repository !== $this->repository)
		{
			throw new EntityAlreadyAttachedException(array($this));
		}
		if ($this->model AND $repository->getModel() !== $this->model)
		{
			throw new EntityAlreadyAttachedException(array($this));
		}
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
		$this->model = false;
	}

	/** Pri klonovani vznika nova entita se stejnejma datama */
	public function __clone()
	{
		if ($this->model === false)
		{
			$this->model = NULL;
		}
		if ($repository = $this->repository)
		{
			$this->repository = NULL;
			$repository->attach($this);
		}
	}

	/**
	 * Repository ktery se o tuto entitu stara.
	 * Existuje jen kdyz entita byla persistovana.
	 * @param bool
	 * @return IRepository |NULL
	 * @throws EntityNotAttachedException
	 */
	final public function getRepository($need = true)
	{
		if (!$this->repository AND $need)
		{
			throw new EntityNotAttachedException(EntityHelper::toString($this) . ' is not attached to repository.');
		}
		return $this->repository;
	}

	/**
	 * @param bool
	 * @return IRepositoryContainer
	 * @throws EntityNotAttachedException
	 */
	final public function getModel($need = true)
	{
		if (!$this->model)
		{
			if ($need === NULL AND !$this->getRepository(false)) // bc
			{
				// trigger_error('Entity::getModel(NULL) is deprecated do not use it.', E_USER_DEPRECATED);
				return RepositoryContainer::get(NULL); // todo di
			}
			if ($this->model === false)
			{
				$this->getRepository($need);
				return NULL;
			}
			if ($r = $this->getRepository($need))
			{
				$this->model = $r->getModel();
			}
		}
		return $this->model;
	}

	/** @deprecated */
	final public function getGeneratingRepository($need = true)
	{
		throw new DeprecatedException(array('Orm\Entity', 'getGeneratingRepository()', 'Orm\Entity', 'getRepository()'));
	}

}
