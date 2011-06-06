<?php

namespace Orm;

interface IMapper
{

	/**
	 * Vsechny entity.
	 * Musi vratit skutecne vsechny entity.
	 * Zadna jina metoda nesmi vratit nejakou entitu kterou by nevratila tato.
	 * @return IEntityCollection
	 */
	public function findAll();

	/**
	 * Vraci kolekci entit dle kriterii.
	 * @see IEntityCollection::findBy()
	 * @return IEntityCollection
	 */
	public function findBy(array $where);

	/**
	 * Vraci jednu entitu dle kriterii.
	 * @see IEntityCollection::getBy()
	 * @return IEntity|NULL
	 */
	public function getBy(array $where);

	/**
	 * @see IRepository::persist()
	 * @param IEntity
	 * @return scalar id
	 */
	public function persist(IEntity $entity);

	/**
	 * @see IRepository::remove()
	 * @param IEntity
	 * @return bool
	 */
	public function remove(IEntity $entity);

	/**
	 * @see IRepository::flush()
	 * @return void
	 */
	public function flush();

	/**
	 * @see IRepository::clean()
	 * @return void
	 */
	public function rollback();

	/**
	 * @see ManyToMany::getMapper()
	 * @param string
	 * @param IRepository
	 * @param string
	 * @return IManyToManyMapper
	 */
	public function createManyToManyMapper($firstParam, IRepository $repository, $secondParam);

	/** @return IRepository */
	public function getRepository();

	/** @return IRepositoryContainer */
	public function getModel();

	/** @return IConventional */
	public function getConventional();

}
