<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/** DI Container Factory */
interface IServiceContainerFactory
{

	/** @return IServiceContainer */
	public function getContainer();

}
