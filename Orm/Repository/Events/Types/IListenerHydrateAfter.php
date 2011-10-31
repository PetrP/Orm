<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * After data from storage are hydrated into entity.
 * Has EventArguments::$data and EventArguments::$entity.
 * It fires after data are hydrated. Entity has all values. If $data are changed there's no effect.
 * @see IRepository::hydrateEntity()
 * @see Events::HYDRATE_AFTER
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerHydrateAfter extends IListener
{
	/** @param EventArguments has $entity and $data */
	public function onAfterHydrateEvent(EventArguments $args);
}
