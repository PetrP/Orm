<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Universal listener.
 * It's listen on all event types.
 *
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events\Types
 */
interface IEventFirer extends IListener
{
	/** @param EventArguments */
	public function fireEvent(EventArguments $args);
}
