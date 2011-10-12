<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Entity will be updated.
 * It fires before.
 * Has EventArguments::$entity.
 * @see IRepository::persist()
 * @see Events::PERSIST_BEFORE_UPDATE
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerPersistBeforeUpdate extends IListener
{
		/** @param EventArguments has $entity */
	public function onBeforePersistUpdateEvent(EventArguments $args);
}
