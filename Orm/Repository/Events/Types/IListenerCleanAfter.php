<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * It fires after repository was cleaned.
 * @see IRepository::clean()
 * @see IRepositoryContainer::clean()
 * @see Events::CLEAN_AFTER
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerCleanAfter extends IListener
{
	/** @param EventArguments */
	public function onAfterCleanEvent(EventArguments $args);
}
