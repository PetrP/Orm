<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Entity is changed and will be saved.
 * It fires before.
 * Has EventArguments::$entity.
 * @see IRepository::persist()
 * @see Events::PERSIST_BEFORE
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerPersistBefore extends IListener
{
		/** @param EventArguments has $entity */
	public function onBeforePersistEvent(EventArguments $args);
}
