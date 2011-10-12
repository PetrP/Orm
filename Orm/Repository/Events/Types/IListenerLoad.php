<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Data from storage are hydrated into entity.
 * Has EventArguments::$data and EventArguments::$entity.
 * It fires before data are hydrated. Entity is empty. $data can be changed. Values at entity will be overwritten.
 * @see IRepository::hydrateEntity()
 * @see Events::LOAD
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerLoad extends IListener
{
		/** @param EventArguments has $entity and $data */
	public function onLoadEvent(EventArguments $args);
}
