<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * It fires after entity was deleted.
 * Has EventArguments::$entity.
 * @see IRepository::remove()
 * @see Events::REMOVE_AFTER
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerRemoveAfter extends IListener
{
	/** @param EventArguments has $entity */
	public function onAfterRemoveEvent(EventArguments $args);
}
