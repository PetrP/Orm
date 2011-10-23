<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * After entity is serialized to storage.
 * Has EventArguments::$values, EventArguments::$operation and EventArguments::$entity.
 * EventArguments::$values contains all scalarized values.
 * EventArguments::$operation contains string insert or update depending on entity is (or not) persisted first time.
 * @see DibiPersistenceHelper::toArray()
 * @see ArrayMapper::flush()
 * @see Events::SERIALIZE_AFTER
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerSerializeAfter extends IListener
{
	/** @param EventArguments has $values, $operation, and $entity */
	public function onAfterSerializeEvent(EventArguments $args);
}
