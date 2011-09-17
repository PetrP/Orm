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
 * Accepted value was not returned.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Common\Exceptions
 */
class BadReturnException extends LogicException
{

	/**
	 * @param string|array
	 * @param int
	 * @param Exception
	 */
	public function __construct($message = NULL, $code = NULL/*§php53*/, Exception $previous = NULL/*php53§*/)
	{
		$message = ExceptionHelper::format($message, "%c1<%1&2%::>%s2<%1&2%()> must return %s3<%!5%, '%t4' given>%s5.");
		parent::__construct($message, $code/*§php53*/, $previous/*php53§*/);
	}
}
