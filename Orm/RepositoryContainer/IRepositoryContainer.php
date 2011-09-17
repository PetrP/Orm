<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

require_once __DIR__ . '/../Orm.php';

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
	 * @return void
	 */
	public function flush();

	/**
	 * Zrusi vsechny zmeny na vsech repository, ale do ukonceni scriptu se zmeny porad drzi.
	 * @return void
	 */
	public function clean();

	/** @return IServiceContainer */
	public function getContext();

}
