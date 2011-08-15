<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use RuntimeException;
use Exception;

class NotValidException extends RuntimeException
{

	/**
	 * @param string|array
	 * @param int
	 * @param Exception
	 */
	public function __construct($message = NULL, $code = NULL, Exception $previous = NULL)
	{
		$message = ExceptionHelper::format($message, "Param %c1::\$%s2 must be '%s3', '%v4' given.");
		parent::__construct($message, $code, $previous);
	}
}
