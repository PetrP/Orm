<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Entity is saved.
 * Has EventArguments::$id and EventArguments::$entity.
 * $id can be changed but it's not recommended.
 * Some relationship are not persisted yet.
 * If entity is changed during Events::PERSIST_AFTER, this event will fired twice.
 * @see IRepository::persist()
 * @see Events::PERSIST
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerPersist extends IListener
{
		/** @param EventArguments has $entity and $id */
	public function onPersistEvent(EventArguments $args);
}
