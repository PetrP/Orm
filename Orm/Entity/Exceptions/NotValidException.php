<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use RuntimeException;
use Exception;

/**
 * Value is not valid for that property of entity.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Exceptions
 */
class NotValidException extends RuntimeException
{

	/**
	 * @param string|array
	 * @param int
	 * @param Exception
	 */
	public function __construct($message = NULL, $code = NULL/*§php53*/, Exception $previous = NULL/*php53§*/)
	{
		$message = ExceptionHelper::format($message, "Param %c1::\$%s2 must be %s3; '%v4' given.");
		parent::__construct($message, $code/*§php53*/, $previous/*php53§*/);
	}
}
