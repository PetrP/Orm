<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Entity was saved.
 * It fires after.
 * Has EventArguments::$entity.
 * If entity is changed during this event, changes will be updated in storage and it fires Events::PERSIST again.
 * @see IRepository::persist()
 * @see Events::PERSIST_AFTER
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerPersistAfter extends IListener
{
		/** @param EventArguments has $entity */
	public function onAfterPersistEvent(EventArguments $args);
}
