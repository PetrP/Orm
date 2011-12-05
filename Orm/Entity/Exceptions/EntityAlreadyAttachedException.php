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
 * Entity is already attached to another {@see IRepositoryContainer}.
 * Use only one RepositoryContainer.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Exceptions
 */
class EntityAlreadyAttachedException extends LogicException
{

	/**
	 * @param string|array
	 * @param int
	 * @param Exception
	 */
	public function __construct($message = NULL, $code = NULL/*§php53*/, Exception $previous = NULL/*php53§*/)
	{
		$message = ExceptionHelper::format($message, "%e1 is already attached to another RepositoryContainer.");
		parent::__construct($message, $code/*§php53*/, $previous/*php53§*/);
	}
}
