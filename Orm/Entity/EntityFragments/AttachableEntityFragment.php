<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\DeprecatedException;

/**
 * Uchovava stav o repository kde je entita attachnuta.
 * @see Entity
 * @property-read IRepositoryContainer $model
 * @todo zvazit jestli to neni spatny navrh
 */
class AttachableEntityFragment extends EventEntityFragment
{
	/** @var IRepository|NULL null kdyz jeste nebylo ulozeno */
	private $repository;

	/** @var IRepositoryContainer|NULL cache */
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
		if (!isset($this->model))
		{
			if ($need === NULL AND !$this->getRepository(false)) // bc
			{
				// trigger_error('Entity::getModel(NULL) is deprecated do not use it.', E_USER_DEPRECATED);
				return RepositoryContainer::get(NULL); // todo di
			}
			if ($r = $this->getRepository($need))
			{
				$this->model = $r->getModel();
			}
		}
		return $this->model;
	}

	/** @deprecated */
	final public function getGeneratingRepository($need = true) { throw new DeprecatedException('Orm\Entity::getGeneratingRepository() is deprecated; use Orm\Entity::getRepository() instead'); }

}
