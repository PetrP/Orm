<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Entity will be saved first time.
 * It fires before.
 * Has EventArguments::$entity.
 * @see IRepository::persist()
 * @see Events::PERSIST_BEFORE_INSERT
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerPersistBeforeInsert extends IListener
{
		/** @param EventArguments has $entity */
	public function onBeforePersistInsertEvent(EventArguments $args);
}
