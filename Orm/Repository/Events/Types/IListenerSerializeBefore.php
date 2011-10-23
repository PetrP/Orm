<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Before entity is serialized to storage.
 * Has EventArguments::$params, EventArguments::$values, EventArguments::$operation and EventArguments::$entity.
 * EventArguments::$params are only in DibiMapper and contains DibiPersistenceHelper::$params.
 * EventArguments::$values contains all entity values given by IEntity::toArray() before then will scalarized.
 * EventArguments::$operation contains string insert or update depending on entity is (or not) persisted first time.
 * @see DibiPersistenceHelper::toArray()
 * @see ArrayMapper::flush()
 * @see Events::SERIALIZE_BEFORE
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerSerializeBefore extends IListener
{
	/** @param EventArguments has $params, $values, $operation, and $entity */
	public function onBeforeSerializeEvent(EventArguments $args);
}
