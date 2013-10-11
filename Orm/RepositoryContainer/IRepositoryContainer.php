<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Collection of IRepository.
 *
 * Cares of initialisation repository.
 * It is entry point into model from other parts of application.
 * Stores container of services what other objects may need.
 * @author Petr Procházka
 * @package Orm
 * @subpackage RepositoryContainer
 */
interface IRepositoryContainer
{

	/**
	 * Dej mi instanci repository.
	 * @param string repositoryName
	 * @return IRepository
	 */
	public function getRepository($name);

	/**
	 * Existuje repository pod timto nazvem?
	 * @param string repositoryName
	 * @return bool
	 */
	public function isRepository($name);

	/**
	 * Registruje repository pod jednodusim nazvem
	 * @param string
	 * @param string
	 * @return IRepositoryContainer
	 */
	public function register($alias, $repositoryClass);

	/**
	 * Promitne vsechny zmeny do uloziste na vsech repository.
	 * @param IRepository|NULL Checks for this repository, if it will be flushed.
	 * @throws RepositoryNotFoundException
	 * @return void
	 */
	public function flush(IRepository $checkRepository = NULL);

	/**
	 * Zrusi vsechny zmeny na vsech repository, ale do ukonceni scriptu se zmeny porad drzi.
	 * @param IRepository|NULL Checks for this repository, if it will be cleaned.
	 * @throws RepositoryNotFoundException
	 * @return void
	 */
	public function clean(IRepository $checkRepository = NULL);

	/**
	 * Persist all unpersist entities at all repositories.
	 * @param IRepository|NULL Checks for this repository, if it will be cleaned.
	 * @return void
	 */
	public function persistAll(IRepository $checkRepository = NULL);

	/** @return IServiceContainer */
	public function getContext();

}
