<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * It fires after repository was flushed.
 * @see IRepository::flush()
 * @see IRepositoryContainer::flush()
 * @see Events::FLUSH_AFTER
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IListenerFlushAfter extends IListener
{
	/** @param EventArguments */
	public function onAfterFlushEvent(EventArguments $args);
}
