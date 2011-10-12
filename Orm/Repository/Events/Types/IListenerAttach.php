<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Entity is attached to repository.
 * Has EventArguments::$entity.
 * @see IRepository::attach()
 * @see Events::ATTACH
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerAttach extends IListener
{
		/** @param EventArguments has $entity */
	public function onAttachEvent(EventArguments $args);
}
