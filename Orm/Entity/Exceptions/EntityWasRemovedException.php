<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;
use LogicException;

/**
 * Entity was removed {@see IContainer::remove()}.
 * Clone entity before reattach to repository.
 * <code>
 * 	$r->remove($e);
 * 	$e = $r->attach(clone $e);
 * </code>
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Exceptions
 */
class EntityWasRemovedException extends LogicException
{

	/**
	 * @param string|array
	 * @param int
	 * @param Exception
	 */
	public function __construct($message = NULL, $code = NULL/*§php53*/, Exception $previous = NULL/*php53§*/)
	{
		$message = ExceptionHelper::format($message, "%e1 was removed. Clone entity before reattach to repository.");
		parent::__construct($message, $code/*§php53*/, $previous/*php53§*/);
	}
}
