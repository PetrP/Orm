<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * It fires before repository will be flushed.
 * @see IRepository::flush()
 * @see IRepositoryContainer::flush()
 * @see Events::FLUSH_BEFORE
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerFlushBefore extends IListener
{
	/** @param EventArguments */
	public function onBeforeFlushEvent(EventArguments $args);
}
