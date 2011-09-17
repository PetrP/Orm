<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use LogicException;
use Exception;

/**
 * Requested method or operation is deprecated.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Common\Exceptions
 */
class DeprecatedException extends LogicException
{

	/**
	 * @param string|array
	 * @param int
	 * @param Exception
	 */
	public function __construct($message = NULL, $code = NULL/*§php53*/, Exception $previous = NULL/*php53§*/)
	{
		$message = ExceptionHelper::format($message, '%c1<%1&2%::>%s2 is deprecated<%3|4%; use %c3<%3&4%::>%s4 instead>.');
		parent::__construct($message, $code/*§php53*/, $previous/*php53§*/);
	}
}
