<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Kolekce Repository.
 * Stara se o jejich vytvareni.
 * Je to vstupni bod do modelu z jinych casti aplikace.
 */
interface IRepositoryContainer
{

	/**
	 * Dej mi instanci repository.
	 * @var string repositoryName
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

	/**
	 * @param IServiceContainer
	 * @return IRepositoryContainer
	 */
	public function setContext(IServiceContainer $container);

}
