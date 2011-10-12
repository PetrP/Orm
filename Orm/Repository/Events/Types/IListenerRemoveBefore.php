<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * It fires before entity will be deleted.
 * Has EventArguments::$entity.
 * @see IRepository::remove()
 * @see Events::REMOVE_BEFORE
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerRemoveBefore extends IListener
{
	/** @param EventArguments has $entity */
	public function onBeforeRemoveEvent(EventArguments $args);
}
