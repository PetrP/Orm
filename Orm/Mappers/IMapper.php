<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Provides mapping between repository and storage.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers
 */
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
	 * @param array
	 * @return IEntityCollection
	 */
	public function findBy(array $where);

	/**
	 * Vraci jednu entitu dle kriterii.
	 * @see IEntityCollection::getBy()
	 * @param array
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
	 * Vytvori mapperu pro m:m asociaci
	 * @see ManyToMany::getMapper()
	 * @param string Nazev parametru na entite ktera patri repository tohodle mapperu
	 * @param IRepository Repository na kterou asociace ukazuje
	 * @param string Parametr na druhe strane kam asociace ukazuje
	 * @return IManyToManyMapper
	 */
	public function createManyToManyMapper($param, IRepository $targetRepository, $targetParam);

	/** @return IRepository */
	public function getRepository();

	/** @return IRepositoryContainer */
	public function getModel();

	/** @return IConventional */
	public function getConventional();

}
