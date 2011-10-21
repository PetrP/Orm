<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * It fires before repository will be cleaned.
 * @see IRepository::clean()
 * @see IRepositoryContainer::clean()
 * @see Events::CLEAN_BEFORE
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerCleanBefore extends IListener
{
	/** @param EventArguments */
	public function onBeforeCleanEvent(EventArguments $args);
}
