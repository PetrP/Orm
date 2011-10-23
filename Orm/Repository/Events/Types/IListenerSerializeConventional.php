<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * After entity is serialized and conventional is applied.
 * Has EventArguments::$values, EventArguments::$operation and EventArguments::$entity.
 * EventArguments::$values contains all scalarized values. Conventional is applied.
 * EventArguments::$operation contains string insert or update depending on entity is (or not) persisted first time.
 * @see DibiPersistenceHelper::toArray()
 * @see ArrayMapper::flush()
 * @see Events::SERIALIZE_CONVENTIONAL
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerSerializeConventional extends IListener
{
	/** @param EventArguments has $values, $operation, and $entity */
	public function onConventionalSerializeEvent(EventArguments $args);
}
