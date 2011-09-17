<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * DI Container Factory.
 * @author Petr Procházka
 * @package Orm
 * @subpackage DI
 */
interface IServiceContainerFactory
{

	/** @return IServiceContainer */
	public function getContainer();

}
